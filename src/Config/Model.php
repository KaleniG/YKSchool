<?php

namespace App\Config;

class Model
{
  private static $db_conn = null;

  private static function initDB()
  {
    if (isset(self::$db_conn)) {
      return "Database connection already initialized";
    } else {
      $conn_string = "host=localhost dbname=ykschool user=postgres password=Gr-dx23Fdg";
      self::$db_conn = pg_connect($conn_string);

      if (!self::$db_conn) {
        self::$db_conn = null;
        return "Failed to initialize the database connection: " . pg_last_error();
      }
    }
  }

  protected static function getConn()
  {
    if (!isset(self::$db_conn)) {
      $result = self::initDB();
      if ($result)
        return $result;
    }

    return self::$db_conn;
  }
}
