<?php

namespace App;

require_once __DIR__ . '/../../vendor/autoload.php';

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LogManager
{
  private static ?Logger $logger = null;

  public static function getLogger()
  {
    if (self::$logger === null) {
      self::$logger = new Logger('ykschool');
      self::$logger->pushHandler(new StreamHandler(__DIR__ . '/../../runtime/app.log', Level::Debug));
    }

    return self::$logger;
  }
}
