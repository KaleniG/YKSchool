<?php

namespace App\Models;

use App\Config\LogManager;
use App\Config\Model;

class AdminManager extends Model
{
  public function validate(Admin $admin)
  {
    $query = "SELECT COUNT(*) FROM administrators WHERE name = $1 AND surname = $2";
    $result = pg_query_params(Model::getConn(), $query, array($admin->name, $admin->surname));

    if (!$result) LogManager::error("Query failed");

    $count = pg_fetch_result($result, 0, 0);
    return $count > 0;
  }

  public function getAllAdmins()
  {
    $query = "SELECT * FROM administrators";
    $result = pg_query(Model::getConn(), $query);

    if (!$result)
      LogManager::error("Query failed");

    return pg_fetch_all($result);
  }

  public function updateChanges($changes)
  {
    pg_prepare(
      Model::getConn(),
      "admin_update",
      "UPDATE administrators SET email=$1, phone_number=$2 WHERE id=$3"
    );
    foreach ($changes as $id => $fields) {
      $email = htmlspecialchars($fields['email']);
      $phone = htmlspecialchars($fields['phone_number']);

      LogManager::info("$id, $email, $phone");
      pg_execute(Model::getConn(), "admin_update", array($email, $phone, $id));
    }
  }

  public function delete($id)
  {
    pg_prepare(Model::getConn(), "admin_delete", "DELETE FROM administrators WHERE id=$1");
    pg_execute(Model::getConn(), "admin_delete", array($id));
  }

  public function add(Admin $admin)
  {
    pg_prepare(Model::getConn(), "admin_add", "INSERT INTO administrators (name, surname, email, phone_number) VALUES ($1, $2, $3, $4)");
    pg_execute(Model::getConn(), "admin_add", array($admin->name, $admin->surname, $admin->email, $admin->phone_number));
  }
}
