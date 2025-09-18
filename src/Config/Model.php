<?php

namespace App\Config;

class Model
{
  private static $db_conn = null;

  private static function initDB()
  {
    if (isset(self::$db_conn)) {
      LogManager::error("Database connection already initialized");
      return;
    } else {
      $conn_string = "host=localhost dbname=ykschool user=postgres password=Gr-dx23Fdg";
      self::$db_conn = pg_connect($conn_string);

      if (!self::$db_conn) {
        self::$db_conn = null;
        LogManager::error("Failed to initialize the database connection: " . pg_last_error());
        return;
      }

      $assets = new AssetManager();
      foreach ($assets->importSQL() as $sql_file) {
        $result = pg_query(self::$db_conn, $sql_file);
        if (!$result) {
          LogManager::error("Error in SQL execution: " . pg_last_error(self::$db_conn));
          return;
        }
      }
    }
  }

  protected static function getConn()
  {
    if (!isset(self::$db_conn))
      self::initDB();

    return self::$db_conn;
  }

  protected static function getError()
  {
    return pg_last_error(self::getConn());
  }
}
