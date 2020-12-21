<?php

namespace Drupal\zero_preprocess\Service;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\zero_preprocess\Base\PreprocessExtenderInterface;

class PreprocessExtenderManager {

  /** @var ClassResolverInterface */
  private $classResolver;
  /** @var string[] */
  private $ids;
  /** @var PreprocessExtenderInterface[] */
  private $extender;

  public function __construct(ClassResolverInterface $class_resolver, array $ids) {
    $this->classResolver = $class_resolver;
    $this->ids = $ids;
  }

  /**
   * @return PreprocessExtenderInterface[]
   */
  public function getExtenders(): array {
    if ($this->extender === NULL) {
      $this->extender = [];
      foreach ($this->ids as $id) {
        $this->extender[$id] = $this->classResolver->getInstanceFromDefinition($id);
      }
      usort($this->extender, function($a, $b) {
        return $a->weight() - $b->weight();
      });
    }
    return $this->extender;
  }

  public function registry(array &$zero, array $item, $name, array $theme_registry) {
    foreach ($this->getExtenders() as $extender) {
      $extender->registry($zero, $item, $name, $theme_registry);
    }
  }

  public function preprocess(array &$vars, array $zero, array $template) {
    foreach ($this->getExtenders() as $extender) {
      $extender->preprocess($vars, $zero, $template);
    }
  }

  public function includePreprocess(&$vars, array $template) {
    if (empty($template['zero']['preprocess'])) return;

    if (!empty($vars['zero']['local'])) {
      foreach ($vars['zero']['local'] as $name => $var) {
        ${$name} = $var;
      }
    }

    /** @noinspection PhpIncludeInspection */
    include $template['zero']['preprocess'];
  }

}