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

  public function getModuleFromClass(string $class): string {
    return explode('\\', $class)[1];
  }

  public function getFileNameFromID(string $id): string {
    return str_replace('_', '-', $id);
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
    $theme = [];
    $definitions = $this->getDefinitions();
    foreach ($definitions as $id => $definition) {
      $module = $this->getModuleFromClass($definition['class']);
      $module_path = $this->moduleHandler->getModule($module)->getPath();
      $theme[$id] = $definition['theme'];
      if (isset($definition['component'])) {
        $theme[$id]['path'] = '/' . $module_path . '/components/' . $this->getFileNameFromID($id) . '/';
      } else {
        $theme[$id]['path'] = '/' . $module_path . '/templates/';
      }
    }
    return $theme;
  }

  public function getLibrariesDefinitions(): array {
    /** @var \Drupal\Core\File\FileSystemInterface $fs */
    $fs = Drupal::service('file_system');

    $libraries = [];

    $definitions = $this->getDefinitions();
    foreach ($definitions as $id => $definition) {
      if (!isset($definition['component'])) continue;

      $name = $this->getFileNameFromID($id);
      $module = $this->getModuleFromClass($definition['class']);
      $module_path = $this->moduleHandler->getModule($module)->getPath();

      if (is_array($definition['component'])) {
        $libraries[$name] = $definition['component'];
      }

      $files = scandir($fs->realpath($module_path . '/components/' . $name));
      foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if ($ext === 'css') {
          $libraries[$name]['css']['component']['../../../' . $module_path . '/components/' . $name . '/' . $file] = [];
        } else if ($ext === 'js') {
          $libraries[$name]['js']['../../../' . $module_path . '/components/' . $name . '/' . $file] = [];
        }
      }
      if (!empty($libraries[$name]) && empty($libraries[$name]['dependencies'])) {
        $libraries[$name]['dependencies'][] = 'zero_preprocess/comp';
      }
    }
    return $libraries;
  }

  public function alterSuggestions(array &$suggestions, array $vars, $hook) {
    $builder = $this->getBuilder($hook);
    if ($builder !== NULL) {
      $builder
        ->setTheme($vars)
        ->setState(ZeroThemeBuilderInterface::STATE_RENDER)
        ->alterSuggestions($suggestions, $vars, $hook);
    }
  }

}
