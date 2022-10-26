<?php
declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

/** @var string $env */

return static function (FrameworkConfig $frameworkConfig) use ($env): void {
      $frameworkConfig->validation()
          ->enabled(true)
          ->emailValidationMode('html5');

      if ($env === 'test') {
          $frameworkConfig->validation()
              ->notCompromisedPassword()
              ->enabled(false);
      }
};
