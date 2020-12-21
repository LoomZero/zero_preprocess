<?php

namespace Drupal\zero_preprocess\Util;

use Drupal;
use Drupal\zero_preprocess\Base\ZeroBase;

class ZeroValueUtil {

  /**
   * @param callable|array|string $value
   * @param ZeroBase|null $context
   */
  public static function getValue(&$value, ZeroBase $context = NULL) {
    if (isset($value['#zero_value'])) return $value['#zero_value'];
    if (is_callable($value)) {
      $value['#zero_value'] = $value($context);
    } else if (is_array($value) && !empty($value['#theme'])) {
      $value['#zero_value'] = ''; // to avoid recursion
      /** @var Drupal\Core\Render\Renderer $renderer */
      $renderer = Drupal::service('renderer');

      $value['#context']['that'] = $context;
      if ($context !== NULL) {
        $value['#context']['context'] = $context->getContext();
      }
      if (isset($value['#after']) && is_callable($value['#after'])) {
        $value['#zero_value'] = $value['#after']($renderer->renderPlain($value)->__toString());
      } else {
        $value['#zero_value'] = $renderer->renderPlain($value)->__toString();
      }
    } else if (is_array($value) && !empty($value['#type']) && $value['#type'] === 'inline_template') {
      $value['#zero_value'] = ''; // to avoid recursion
      /** @var Drupal\Core\Render\Renderer $renderer */
      $renderer = Drupal::service('renderer');

      $value['#context']['that'] = $context;
      if ($context !== NULL) {
        $value['#context']['context'] = $context->getContext();
      }
      if (isset($value['#after']) && is_callable($value['#after'])) {
        $value['#zero_value'] = $value['#after']($renderer->renderPlain($value)->__toString());
      } else {
        $value['#zero_value'] = $renderer->renderPlain($value)->__toString();
      }
    } else {
      return $value;
    }
    return $value['#zero_value'];
  }

  /**
   * @param string $template
   * @param callable|array $context
   *
   * @return array
   */
  public function template(string $template, $context = []) {
    return [
      '#type' => 'inline_template',
      '#template' => $template,
      '#context' => $context,
    ];
  }

}