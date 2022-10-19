<?php

namespace Drupal\zero_preprocess\Base;

use Drupal\Core\Render\RenderableInterface;

interface ZeroThemeBuilderInterface extends RenderableInterface {

  public const STATE_RENDER = 'render';
  public const STATE_NORMAL = 'normal';

  public function setState(string $state, array &$render = []): self;

  public function getState(): string;

  public function setTheme(array $theme): self;

  public function defTheme($existing, $type, $theme, $path): array;

  public function alterSuggestions(array &$suggestions, array $vars, $hook);

  public function preprocess(&$vars);

}
