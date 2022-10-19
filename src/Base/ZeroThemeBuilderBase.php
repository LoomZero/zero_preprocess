<?php

namespace Drupal\zero_preprocess\Base;

use Drupal;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\zero_entitywrapper\Base\ContentWrapperInterface;
use Drupal\zero_entitywrapper\Content\ContentWrapper;

abstract class ZeroThemeBuilderBase extends PluginBase implements ZeroThemeBuilderInterface {

  protected array $theme = [];
  protected array $render = [];
  protected string $state = ZeroThemeBuilderInterface::STATE_NORMAL;

  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->theme['theme'] = $plugin_id;
  }

  public function setState(string $state, array &$render = []): self {
    $this->state = $state;
    $this->render = &$render;
    return $this;
  }

  public function getState(): string {
    return $this->state;
  }

  public function setTheme(array $theme): ZeroThemeBuilderInterface {
    $this->theme = [];
    foreach ($theme as $key => $value) {
      if (str_starts_with($key, '#')) $key = substr($key, 1);
      $this->theme[$key] = $value;
    }
    return $this;
  }

  public function defTheme($existing, $type, $theme, $path): array {
    return ['variables' => []];
  }

  public function alterSuggestions(array &$suggestions, array $vars, $hook) { }

  public function preprocess(&$vars) { }

  public function toRenderable() {
    if ($this->validate($this->theme)) {
      $renderable = [];
      foreach ($this->theme as $key => $value) {
        $renderable['#' . $key] = $value;
      }
      return $renderable;
    } else {
      return [
        '#markup' => 'Invalid theme function "' . $this->getPluginId() . '"',
      ];
    }
  }

  /**
   * @param array $theme
   *
   * @return bool
   */
  public function validate(array $theme): bool {
    if (!empty($this->getPluginDefinition()['validate'])) {
      if (!empty($this->getPluginDefinition()['validate']['required'])) {
        foreach ($this->getPluginDefinition()['validate']['required'] as $field => $warning) {
          if (empty($theme[$field])) {
            Drupal::messenger()->addWarning(strtoupper('[' . $this->getPluginId() . ']: ') . $warning);
            return FALSE;
          }
        }
      }
    }
    return TRUE;
  }

  public function addLibrary(string $context, string $library = NULL): self {
    if ($library === NULL) {
      $this->render['#attached']['library'][] = $context;
    } else {
      $this->render['#attached']['library'][] = $context . '/' . $library;
    }
    return $this;
  }

  public function getUUID(): string {
    if (empty($this->render['uuid'])) {
      $uuidGenerator = Drupal::service('uuid');
      $this->render['uuid'] = $uuidGenerator->generate();
    }
    return $this->render['uuid'];
  }

  public function addSettings(array $settings = []): self {
    $uuid = $this->getUUID();
    if (isset($this->render['#attached']['drupalSettings']['zero']['settings'][$uuid])) {
      $this->render['#attached']['drupalSettings']['zero']['settings'][$uuid] = array_merge($this->render['#attached']['drupalSettings']['zero']['settings'][$uuid], $settings);
    } else {
      $this->render['#attached']['drupalSettings']['zero']['settings'][$uuid] = $settings;
    }
    return $this;
  }

  /**
   * @param array|EntityInterface|ContentWrapperInterface $entity
   *
   * @return ContentWrapperInterface|null
   */
  protected function toContentWrapper($entity): ?ContentWrapperInterface {
    if (is_array($entity) && isset($entity['entity_type']) && isset($entity['id'])) {
      return ContentWrapper::load($entity['entity_type'], $entity['id']);
    } else if ($entity instanceof ContentEntityBase) {
      return ContentWrapper::create($entity);
    } else if ($entity instanceof ContentWrapperInterface) {
      return $entity;
    }
    return NULL;
  }

}
