<?php

namespace App\Models;

use App\Config\LogManager;
use App\Config\Model;

class TeacherManager extends Model
{
  public function validate(Teacher $teacher)
  {
    $query = "SELECT COUNT(*) FROM teachers WHERE name = $1 AND surname = $2";
    $result = pg_query_params(Model::getConn(), $query, array($teacher->name, $teacher->surname));

    if (!$result) LogManager::error("Query failed");

    $count = pg_fetch_result($result, 0, 0);
    return $count > 0;
  }

  public function getAllTeachers()
  {
    $query = "SELECT * FROM teachers";
    $result = pg_query(Model::getConn(), $query);

    if (!$result)
      LogManager::error("Query failed");

    return pg_fetch_all($result);
  }

  public function updateChanges($changes)
  {
    pg_prepare(
      Model::getConn(),
      "teachers_update",
      "UPDATE teachers SET email=$1, phone_number=$2 WHERE id=$3"
    );
    foreach ($changes as $id => $fields) {
      $email = $fields['email'];
      $phone = $fields['phone_number'];
      pg_execute(Model::getConn(), "teachers_update", array($email, $phone, $id));
    }
  }

  public function delete($id)
  {
    pg_prepare(Model::getConn(), "teachers_delete", "DELETE FROM teachers WHERE id=$1");
    pg_execute(Model::getConn(), "teachers_delete", array($id));
  }

  public function add(Teacher $teacher)
  {
    pg_prepare(Model::getConn(), "teachers_add", "INSERT INTO teachers (name, surname, email, phone_number) VALUES ($1, $2, $3, $4)");
    pg_execute(Model::getConn(), "teachers_add", array($teacher->name, $teacher->surname, $teacher->email, $teacher->phone_number));
  }
}
