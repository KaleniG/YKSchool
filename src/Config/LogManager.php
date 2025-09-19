<?php

namespace App\Config;

require_once Path::autoloader();

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LogManager
{
  private static ?Logger $logger = null;

  private static function getLogger()
  {
    if (self::$logger === null) {
      $sessionId = session_id();
      if (empty($sessionId)) {
        $sessionId = 'no-session';
      }
      $logFile = __DIR__ . "/../../runtime/app_session_{$sessionId}.log";

      self::$logger = new Logger('ykschool');
      self::$logger->pushHandler(new StreamHandler($logFile, Level::Debug));
    }

    return self::$logger;
  }

  public static function error($string)
  {
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
    $callerFile = $backtrace[1]['file'] ?? 'unknown file';
    self::getLogger()->error("[$callerFile] " . $string);
    header("Location: runtime/");
    exit;
  }

  public static function info($string)
  {
    self::getLogger()->info($string);
  }
}
