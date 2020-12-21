<?php

namespace Drupal\zero_preprocess\Base;

interface ZeroEventableInterface {

  public function addListener(string $event, callable $listener);

  public function trigger(string $event, array $args = []);

}