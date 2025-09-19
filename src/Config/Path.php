<?php

namespace App\Config;

class Path
{
  private static $config = __DIR__ . "/";
  private static $views = __DIR__ . "/../Views/";
  private static $controllers = __DIR__ . "/../Controllers/";
  private static $models = __DIR__ . "/../Models/";
  private static $autoloader = __DIR__ . "/../../vendor/autoload.php";

  public static function config($string)
  {
    return Path::$config . $string;
  }

  public static function views($string)
  {
    return Path::$views . $string;
  }

  public static function controllers($string)
  {
    return Path::$controllers . $string;
  }

  public static function models($string)
  {
    return Path::$models . $string;
  }

  public static function autoloader()
  {
    return Path::$autoloader;
  }
}
