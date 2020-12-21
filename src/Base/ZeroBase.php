<?php

namespace Drupal\zero_preprocess\Base;

use Drupal;
use Drupal\zero_preprocess\Util\ZeroValueUtil;

abstract class ZeroBase implements ZeroEventableInterface, ZeroLogableInterface {

  private $listeners = [];
  private $logger = [];

  public function logChannel(): string {
    return 'zero_preprocess';
  }

  public function addListener(string $event, callable $listener) {
    $this->listeners[$event][] = $listener;
  }

  public function trigger(string $event, array $args = []) {
    $args['event'] = $event;
    if (!empty($this->listeners[$event])) {
      foreach ($this->listeners[$event] as $listener) {
        $listener($this, $args);
      }
    }
    $this->doLog($event, $args);
  }

  public function log(string $event, string $message_template = NULL, string $mode = self::LOG_MODE_NORMAL) {
    if ($message_template === NULL) {
      $this->logger[$event]['mode'] = $mode;
    } else {
      $this->logger[$event] = [
        'message' => $message_template,
        'mode' => $mode,
      ];
    }
  }

  public function doLog(string $type, array $args = []) {
    if (!empty($this->logger[$type]) && $this->logger[$type]['mode'] !== self::LOG_MODE_SILENT) {
      $log = [
        '#type' => 'inline_template',
        '#template' => $this->logger[$type]['message'],
        '#context' => $args,
      ];
      $message = ZeroValueUtil::getValue($log, $this);

      if ($this->logger[$type]['mode'] === self::LOG_MODE_NORMAL || $this->logger[$type]['mode'] === self::LOG_MODE_VERBOSE) {
        Drupal::logger($this->logChannel())->notice(nl2br($message . "\n" . print_r($args, TRUE)));
      }
      if ($this->logger[$type]['mode'] === self::LOG_MODE_VERBOSE) {
        Drupal::messenger()->addStatus($message);
      }
    }
  }

  public function getContext(): array {
    return [];
  }

}