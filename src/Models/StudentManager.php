<?php

namespace App\Models;

use App\Config\Log;
use App\Config\Model;

class StudentManager extends Model
{
  private static $prepared = false;

  public function prepareAll()
  {
    if (StudentManager::$prepared) return;

    pg_prepare(
      Model::getConn(),
      "get_all_students",
      "SELECT * FROM students 
      ORDER BY id ASC"
    );

    pg_prepare(
      Model::getConn(),
      "get_student",
      "SELECT * FROM students 
      WHERE id=$1"
    );

    pg_prepare(
      Model::getConn(),
      "student_delete",
      "DELETE FROM students 
      WHERE id=$1"
    );

    pg_prepare(
      Model::getConn(),
      "student_add",
      "INSERT INTO students (name, surname, email, phone_number, tuition_enabled) 
      VALUES ($1, $2, $3, $4, $5)"
    );

    StudentManager::$prepared = true;
  }

  public function validate(Student $student)
  {
    pg_prepare(
      Model::getConn(),
      "student_validate",
      "SELECT *
      FROM students
      WHERE name=$1 AND surname=$2"
    );

    $result = pg_execute(
      Model::getConn(),
      "student_validate",
      [$student->name, $student->surname]
    );

    if (!$result) Log::error("Query failed: " . Model::getError());

    $user = pg_fetch_assoc($result, 0);

    if (isset($user) && !empty($user)) {
      $student->id = $user["id"];
      $student->name = $user["name"];
      $student->surname = $user["surname"];
      $student->email = $user["email"];
      $student->phone_number = $user["phone_number"];
      $student->tuition_enabled = ($user["tuition_enabled"] == "t") ? true : false;

      return $student;
    } else
      return false;
  }

  public function getAllStudents()
  {
    $this->prepareAll();

    $result = pg_execute(
      Model::getConn(),
      "get_all_students",
      []
    );

    if (!$result) Log::error("Query failed: " . Model::getError());

    return pg_fetch_all($result);
  }

  public function getStudent($id)
  {
    $this->prepareAll();

    if (!isset($id))
      Log::error("Invalid student get parameters");

    $result = pg_execute(Model::getConn(), "get_student", [$id]);

    if (!$result) Log::error("Query failed");

    $student = pg_fetch_assoc($result);

    return [
      "id" => $student["id"],
      "name" => $student["name"],
      "surname" => $student["surname"],
      "email" => $student["email"],
      "phone_number" => $student["phone_number"],
      "tuition_enabled" => $student["tuition_enabled"]
    ];
  }

  public function update($changes)
  {
    $this->prepareAll();

    if (!isset($changes["id"]))
      Log::error("Invalid student update parameters");

    $id = $changes["id"];
    $fields = [];
    $values = [];

    if (array_key_exists("email", $changes)) {
      $fields[] = "email = $" . (count($values) + 1);
      $values[] = $changes["email"];
    }
    if (array_key_exists("phone_number", $changes)) {
      $fields[] = "phone_number = $" . (count($values) + 1);
      $values[] = $changes["phone_number"];
    }
    if (array_key_exists("tuition_enabled", $changes)) {
      $fields[] = "tuition_enabled = $" . (count($values) + 1);
      $values[] = ($changes["tuition_enabled"]) ? "t" : "f";
    }

    if (empty($fields))
      return;

    $values[] = $id;
    $sql = "UPDATE students SET " . implode(", ", $fields) . " WHERE id=$" . count($values);

    $result = pg_query_params(Model::getConn(), $sql, $values);

    if (!$result) Log::error("Query failed: " . Model::getError());
  }

  public function delete($id)
  {
    $this->prepareAll();

    if (!isset($id))
      Log::error("Invalid student delete parameter");

    $result = pg_execute(
      Model::getConn(),
      "student_delete",
      [$id]
    );

    if (!$result) Log::error("Query failed: " . Model::getError());
  }

  public function add(Student $student)
  {
    $this->prepareAll();

    $result = pg_execute(
      Model::getConn(),
      "student_add",
      [$student->name, $student->surname, $student->email, $student->phone_number, (($student->tuition_enabled) ? "t" : "f")]
    );

    if (!$result) Log::error("Query failed: " . Model::getError());
  }
}
