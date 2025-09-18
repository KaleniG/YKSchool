<?php

namespace App\Config;

class Path
{
  private static $administrator = __DIR__ . "/../Interfaces/Administration/";
  private static $common = __DIR__ . "/../Interfaces/Common/";
  private static $teacher = __DIR__ . "/../Interfaces/Teacher/";
  private static $student = __DIR__ . "/../Interfaces/Student/";
  private static $autoloader = __DIR__ . "/../../vendor/autoload.php";

  public static function administrator($string)
  {
    return Path::$administrator . $string;
  }

  public static function common($string)
  {
    return Path::$common . $string;
  }

  public static function teacher($string)
  {
    return Path::$teacher . $string;
  }

  public static function student($string)
  {
    return Path::$student . $string;
  }

  public static function autoloader()
  {
    return Path::$autoloader;
  }
}
