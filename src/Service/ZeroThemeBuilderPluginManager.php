<?php

namespace Drupal\zero_preprocess\Service;

use Drupal;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\zero_preprocess\Base\ZeroThemeBuilderInterface;

class ZeroThemeBuilderPluginManager extends DefaultPluginManager {

  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/Zero/ThemeBuilder', $namespaces, $module_handler,
      'Drupal\zero_preprocess\Base\ZeroThemeBuilderInterface',
      'Drupal\zero_preprocess\Annotation\ZeroThemeBuilder');

    $this->alterInfo('zero_theme_builder_info');
    $this->setCacheBackend($cache_backend, 'zero_theme_builder_info');
  }

  public function getBuilder(string $id): ?ZeroThemeBuilderInterface {
    if ($this->getDefinition($id, FALSE) === NULL) return NULL;
    /** @noinspection PhpIncompatibleReturnTypeInspection */
    return $this->createInstance($id);
  }

  public function toBuilder(array $theme): ?ZeroThemeBuilderInterface {
    $builder = $this->getBuilder($theme['#theme']);
    return $builder ? $builder->setTheme($theme) : NULL;
  }

  public function getThemeFunctionsDefinition(): array {
    /** @var ModuleHandlerInterface $module_handler */
    $module_handler = Drupal::service('module_handler');

    $theme = [];
    $definitions = $this->getDefinitions();
    foreach ($definitions as $id => $definition) {
      [ $drupal, $module ] = explode('\\', $definition['class']);
      $module_path = $module_handler->getModule($module)->getPath();
      $theme[$id] = $definition['theme'];
      $theme[$id]['path'] = '/' . $module_path . '/templates/';
    }
    return $theme;
  }

  public function alterSuggestions(array &$suggestions, array $vars, $hook) {
    $builder = $this->getBuilder($hook);
    if ($builder !== NULL) {
      $builder
        ->setState(ZeroThemeBuilderInterface::STATE_RENDER)
        ->setTheme($vars)
        ->alterSuggestions($suggestions, $vars, $hook);
    }
  }

}
