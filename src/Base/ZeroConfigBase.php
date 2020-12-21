<?php

namespace Drupal\zero_preprocess\Base;

use Drupal\zero_preprocess\Service\ZeroConfigManager;

abstract class ZeroConfigBase {

  public function weight(): int {
    return 0;
  }

  public function load(ZeroConfigManager $manager, $name, $value, array $info) {
    return $value;
  }

  public function getNamespace(): string {
    return explode(':', $this->namespace())[0];
  }

  public abstract function namespace(): string;

  public abstract function config(): array;

}