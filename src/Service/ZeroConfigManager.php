<?php

namespace Drupal\zero_preprocess\Service;

use Closure;
use Drupal;
use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\zero_preprocess\Base\ZeroConfigBase;

class ZeroConfigManager {

  /** @var ClassResolverInterface */
  private $classResolver;
  /** @var string[] */
  private $ids;
  /** @var ZeroConfigBase[] */
  private $extenders;

  private $state;
  private $config;

  private $info;
  private $bag;

  public function __construct(ClassResolverInterface $class_resolver, array $ids) {
    $this->classResolver = $class_resolver;
    $this->ids = $ids;
  }

  /**
   * @return ZeroConfigBase[]
   */
  public function getExtenders(): array {
    if ($this->extenders === NULL) {
      $this->extenders = [];
      foreach ($this->ids as $id) {
        $extender = $this->classResolver->getInstanceFromDefinition($id);
        $this->extenders[$extender->getNamespace()] = $extender;
      }
      uasort($this->extenders, function($a, $b) {
        return $a->weight() - $b->weight();
      });
    }
    return $this->extenders;
  }

  private function ensure() {
    if ($this->bag === NULL) {
      $this->info = [];
      $this->bag = [];
      $data = [];
      foreach ($this->getExtenders() as $extender) {
        $data[$extender->namespace()] = $extender->config();
      }
      $this->doInfo($data, $this->info, $this->bag);
    }
    return $this->bag;
  }

  private function doInfo($array, &$info, &$bag, $stack = []) {
    foreach ($array as $index => $value) {
      $is_array = is_array($value);
      $data = $this->parseInfo($index, $is_array, $stack);

      if ($is_array) {
        $info[$data['key']] = $data;
        $new_stack = $stack;
        $new_stack[] = $data['key'];
        $this->doInfo($value, $info[$data['key']]['children'], $bag[$data['key']], $new_stack);
      } else {
        $bag[$data['key']] = $value;
        $info[$data['key']] = $data;
      }
    }
  }

  private function parseInfo(string $index, bool $wrapper = FALSE, array $stack = []): array {
    $data = [];

    if ($wrapper) {
      $type = 'array';
      [ $key, $title, $description ] = explode(':', $index);
      if (strpos($key, '+') === 0) {
        $data['fieldset'] = 'open';
        $key = substr($key, 1);
      } else if (strpos($key, '-') === 0) {
        $data['fieldset'] = 'close';
        $key = substr($key, 1);
      } else {
        $data['fieldset'] = 'none';
      }
      $data['children'] = [];
    } else {
      $items = explode(':', $index);
      $key = $items[0] ?? NULL;
      $type = $items[1] ?? NULL;
      $title = $items[2] ?? NULL;
      $description = $items[3] ?? NULL;

      if (strpos($key, '@') === 0) {
        $data['state'] = TRUE;
        $key = substr($key, 1);
      } else {
        $data['state'] = FALSE;
      }
    }

    $stack[] = $key;

    $data['key'] = $key;
    $data['type'] = $type;
    $data['title'] = $title;
    $data['description'] = $description;
    $data['stack'] = implode('.', $stack);
    $data['namespace'] = $stack[0];

    return $data;
  }

  private function getKeys($name) {
    return explode('.', $name);
  }

  private function state() {
    if ($this->state === NULL) {
      $this->state = Drupal::state()->get('zero_preprocess');
    }
    return $this->state;
  }

  private function config() {
    if ($this->config === NULL) {
      $this->config = Drupal::config('zero_preprocess.config');
    }
    return $this->config;
  }

  private function doRun($info, Closure $function) {
    if ($info['type'] === 'array') {
      $result = [];
      foreach ($info['children'] as $index => $child) {
        $result[$index] = $this->doRun($child, $function);
      }
      return $result;
    } else {
      return $function($info);
    }
  }

  public function info() {
    $this->ensure();
    return $this->info;
  }

  public function bag() {
    $this->ensure();
    return $this->bag;
  }

  public function get(string $name) {
    return $this->doRun($this->getInfo($name), function($info) use ($name) {
      $value = NULL;
      if ($info['state']) {
        $value = $this->getNested($this->state(), $info['stack']);
      } else {
        $value = $this->config()->get($info['stack']);
      }
      if ($value === NULL) {
        return $this->getNested($this->bag(), $info['stack']);
      }
      return $this->getExtenders()[$info['namespace']]->load($this, $name, $value, $info);
    });
  }

  public function getInfo(string $name) {
    $info = $this->info();
    $keys = $this->getKeys($name);
    $first = array_shift($keys);

    $info = $info[$first];
    foreach ($keys as $key) {
      if (!isset($info['children'][$key])) return NULL;
      $info = $info['children'][$key];
    }
    return $info;
  }

  public function getNested($data, string $nested) {
    foreach ($this->getKeys($nested) as $key) {
      if (!isset($data[$key])) return NULL;
      $data = $data[$key];
    }
    return $data;
  }

}