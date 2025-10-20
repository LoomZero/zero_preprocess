<?php

namespace Drupal\zero_preprocess\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * @see \Drupal\zero_importer\Service\ZeroImporterPluginManager
 * @see plugin_api
 *
 * @Annotation
 */
class PreprocessPluginBuilder extends Plugin {

  /** @var string */
  public $id;

  /** @var string */
  public $label;

  /** @var string */
  public $description;

}
