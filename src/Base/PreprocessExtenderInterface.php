<?php

namespace Drupal\zero_preprocess\Base;

interface PreprocessExtenderInterface {

  public function weight(): int;

  public function config(): array;

  public function registry(array &$zero, array $item, $name, array $theme_registry);

  public function preprocess(array &$vars, array $zero, array $template);

}