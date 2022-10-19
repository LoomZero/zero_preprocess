<?php

namespace Drupal\zero_preprocess\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * @see \Drupal\zero_importer\Service\ZeroImporterPluginManager
 * @see plugin_api
 *
 * @Annotation
 */
class ZeroThemeBuilder extends Plugin {

  /** @var string */
  public $id;

  /** @var array|bool */
  public $component;

  /** @var array */
  public $theme;

  /** @var array */
  public $validate;

}
