<?php

namespace App\Interfaces\Common\Models;

use App\Config\LogManager;
use App\Config\Model;
use Exception;

class UserValidator extends Model
{
  public function validate($table, $name, $surname)
  {
    $allowedTables = ['administrators', 'teachers', 'students'];
    if (!in_array($table, $allowedTables)) {
      return false;
    }

    $query = "SELECT COUNT(*) FROM {$table} WHERE name = $1 AND surname = $2";
    $result = pg_query_params(Model::getConn(), $query, array($name, $surname));

    if (!$result) {
      return false;
    }

    $count = pg_fetch_result($result, 0, 0);
    return $count > 0;
  }
}
