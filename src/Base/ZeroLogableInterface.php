<?php

namespace Drupal\zero_preprocess\Base;

interface ZeroLogableInterface {

  const LOG_MODE_VERBOSE = 'verbose';
  const LOG_MODE_NORMAL = 'normal';
  const LOG_MODE_SILENT = 'silent';

  public function logChannel(): string;

  public function doLog(string $type, array $args = []);

  public function log(string $event, string $message_template = NULL, string $mode = self::LOG_MODE_NORMAL);

}