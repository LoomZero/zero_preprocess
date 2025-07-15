<?php

namespace Drupal\zero_preprocess\Service;

use Drupal;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

class PreprocessExtenderPluginManager extends DefaultPluginManager {

  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/Zero/Preprocess', $namespaces, $module_handler,
      'Drupal\zero_preprocess\Base\PreprocessPluginBuilderInterface',
      'Drupal\zero_preprocess\Annotation\PreprocessPluginBuilder');

    $this->alterInfo('zero_preprocess_plugin_builder_info');
    $this->setCacheBackend($cache_backend, 'zero_preprocess_plugin_builder_info');
  }


  /**
   * @return Drupal\zero_preprocess\Base\PreprocessPluginBuilderInterface[]
   */
  public function getExtenders(): array {
    $manager = Drupal::service('plugin.manager.preprocess_extender');
    $extender = [];
    forEach($manager->getDefinitions() as $pluginId => $definition) {
      $extender[] = $manager->createInstance($pluginId);
    }
    usort($extender, function($a, $b) {
      return $a->weight() - $b->weight();
    });
    return $extender;
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
