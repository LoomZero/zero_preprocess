<?php

namespace Drupal\zero_preprocess\Collection;

use ArrayObject;

class ProxyCollection extends ArrayObject {

  public function __call($name, $arguments) {
    $results = [];
    foreach ($this as $item) {
      if (method_exists($item, $name)) {
        $value = $item->{$name}(...$arguments);
        if ($value instanceof ProxyCollection) {
          foreach ($value as $value_delta => $value_item) {
            $results[] = $value_item;
          }
        } else {
          $results[] = $value;
        }
      }
    }
    return new self($results);
  }

  public function array() {
    return $this->getArrayCopy();
  }

  public function arrayCall(callable $array_function, ...$args) {
    $array = $this->exchangeArray([]);
    $response = $array_function($array, ...$args);
    if (is_array($response)) {
      $this->exchangeArray($response);
      return $this;
    }
    return $response;
  }

  public function itemCall(callable $array_function, ...$args) {
    $array = $this->exchangeArray([]);
    $response = $array_function($array, ...$args);
    $this->exchangeArray($array);
    return $response;
  }

  public function join($glue = ' ') {
    return implode($glue, $this->getArrayCopy());
  }

  public function map(callable $callback, ...$args): array {
    return array_map($callback, $this->getArrayCopy(), ...$args);
  }

  public function filter(callable $callback = NULL, int $flag = 0) {
    return $this->arrayCall('array_filter', $callback, $flag);
  }

  public function reduce(callable $callback, $initial = NULL) {
    return $this->arrayCall('array_reduce', $callback, $initial);
  }

  public function reverse(bool $preserve_keys = FALSE) {
    return $this->arrayCall('array_reverse', $preserve_keys);
  }

  public function unique(int $sort_flags = SORT_STRING) {
    $this->arrayCall('array_unique', $sort_flags);
    return $this;
  }

  public function slice(int $offset, int $length = NULL, bool $preserve_keys = FALSE): array {
    return $this->itemCall('array_slice', $offset, $length, $preserve_keys);
  }

  public function splice(int $offset , int $length = NULL, $replacement = []): array {
    return $this->itemCall('array_slice', $offset, $length, $replacement);
  }

  public function shift() {
    return $this->itemCall('array_shift');
  }

  public function unshift(...$elements) {
    $this->itemCall('array_unshift', ...$elements);
    return $this;
  }

  public function pop() {
    return $this->itemCall('array_pop');
  }

  public function push(...$elements) {
    $this->itemCall('array_push', ...$elements);
    return $this;
  }

  public function first() {
    return $this->itemCall('reset');
  }

  public function last() {
    return $this->itemCall('end');
  }

  public function firstKey() {
    $array = $this->exchangeArray([]);
    reset($array);
    $response = key($array);
    $this->exchangeArray($array);
    return $response;
  }

  public function lastKey() {
    $array = $this->exchangeArray([]);
    end($array);
    $response = key($array);
    $this->exchangeArray($array);
    return $response;
  }

  public function length() {
    return count($this);
  }

  public function remove(int $offset, int $length = 1) {
    $this->splice($offset, $length);
    return $this;
  }

  public function pushAt(int $offset, array $elements) {
    $this->splice($offset, 0, $elements);
    return $this;
  }

}
