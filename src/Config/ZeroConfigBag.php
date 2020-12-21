<?php

namespace Drupal\zero_preprocess\Config;

use Drupal\zero_preprocess\Base\ZeroConfigBase;

class ZeroConfigBag extends ZeroConfigBase {

  public function namespace(): string {
    return 'zero:Titel:Description';
  }

  public function config(): array {
    return [
      'name:string:Titel:Description' => 'Hallo',
      '+cool::Description 2' => [
        'huhu:string' => 'OK',
        '@state:bool' => TRUE,
      ],
    ];
  }

}