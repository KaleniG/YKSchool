<?php

namespace App\Models;

use App\Config\LogManager;
use App\Config\Model;

class AdminManager extends Model
{
  private static $prepared = false;

  public function prepareAll()
  {
    if (AdminManager::$prepared) return;

    pg_prepare(
      Model::getConn(),
      "get_all_admins",
      "SELECT * FROM administrators ORDER BY id ASC"
    );

    pg_prepare(
      Model::getConn(),
      "admin_delete",
      "DELETE FROM administrators 
      WHERE id=$1"
    );

    pg_prepare(
      Model::getConn(),
      "admin_add",
      "INSERT INTO administrators (name, surname, email, phone_number) 
      VALUES ($1, $2, $3, $4)"
    );

    AdminManager::$prepared = true;
  }

  public function validate(Admin $admin)
  {
    pg_prepare(
      Model::getConn(),
      "admin_validate",
      "SELECT * FROM administrators 
      WHERE name = $1 AND surname = $2"
    );

    $result = pg_execute(
      Model::getConn(),
      "admin_validate",
      [$admin->name, $admin->surname]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    $user = pg_fetch_assoc($result, 0);

    if (isset($user) && !empty($user)) {
      $admin->id = $user["id"];
      $admin->name = $user["name"];
      $admin->surname = $user["surname"];
      $admin->email = $user["email"];
      $admin->phone_number = $user["phone_number"];
      return $admin;
    } else
      return false;
  }

  public function getAllAdmins()
  {
    /*
    pg_prepare(
      Model::getConn(),
      "get_all_admins",
      "SELECT * FROM administrators"
    );
    */

    $result = pg_execute(
      Model::getConn(),
      "get_all_admins",
      []
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    return pg_fetch_all($result);
  }

  public function updateChanges($changes) // Legacy
  {
    pg_prepare(
      Model::getConn(),
      "admin_update",
      "UPDATE administrators SET email=$1, phone_number=$2 WHERE id=$3"
    );
    foreach ($changes as $id => $fields) {
      $email = htmlspecialchars($fields['email']);
      $phone = htmlspecialchars($fields['phone_number']);
      pg_execute(Model::getConn(), "admin_update", array($email, $phone, $id));
    }
  }

  public function update($changes)
  {
    if (!isset($changes["id"]))
      LogManager::error("Invalid admin update parameters");

    $id = $changes["id"];
    $fields = [];
    $values = [];

    if (array_key_exists("email", $changes)) {
      $fields[] = "email=$" . (count($values) + 1);
      $values[] = $changes["email"];
    }
    if (array_key_exists("phone_number", $changes)) {
      $fields[] = "phone_number=$" . (count($values) + 1);
      $values[] = $changes["phone_number"];
    }

    if (empty($fields))
      return;

    $values[] = $id;
    $sql = "UPDATE administrators SET " . implode(', ', $fields) . " WHERE id=$" . count($values);

    $result = pg_query_params(Model::getConn(), $sql, $values);

    if (!$result) LogManager::error("Query failed: " . Model::getError());
  }

  public function delete($id)
  {
    if (!isset($id))
      LogManager::error("Invalid admin delete parameter");

    /*
    pg_prepare(
      Model::getConn(),
      "admin_delete",
      "DELETE FROM administrators 
      WHERE id=$1"
    );
*/
    $result = pg_execute(
      Model::getConn(),
      "admin_delete",
      [$id]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());
  }

  public function add(Admin $admin)
  {
    /*
    pg_prepare(
      Model::getConn(),
      "admin_add",
      "INSERT INTO administrators (name, surname, email, phone_number) 
      VALUES ($1, $2, $3, $4)"
    );*/

    $result = pg_execute(
      Model::getConn(),
      "admin_add",
      [$admin->name, $admin->surname, $admin->email, $admin->phone_number]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());
  }
}
