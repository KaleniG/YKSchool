<?php

namespace App\Models;

use App\Config\LogManager;
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

    if (!$result) LogManager::error("Query failed: " . Model::getError());

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
    $result = pg_execute(
      Model::getConn(),
      "get_all_students",
      []
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    return pg_fetch_all($result);
  }

  public function getStudent($id)
  {
    if (!isset($id))
      LogManager::error("Invalid student get parameters");

    $result = pg_execute(Model::getConn(), "get_student", [$id]);

    if (!$result) LogManager::error("Query failed");

    $student = pg_fetch_assoc($result);

    return [
      "id" => $student['id'],
      "name" => $student['name'],
      "surname" => $student['surname'],
      "email" => $student['email'],
      "phone_number" => $student['phone_number'],
      "tuition_enabled" => $student['tuition_enabled']
    ];
  }

  public function updateChanges($changes)
  {
    pg_prepare(
      Model::getConn(),
      "students_update",
      "UPDATE students SET email=$1, phone_number=$2, tuition_enabled=$3 WHERE id=$4"
    );
    foreach ($changes as $id => $fields) {
      $email = htmlspecialchars($fields['email']);
      $phone = htmlspecialchars($fields['phone_number']);
      $tuition_enabled = htmlspecialchars($fields['tuition_enabled']);

      pg_execute(Model::getConn(), "students_update", array($email, $phone, $tuition_enabled, $id));
    }
  }

  public function update($changes)
  {
    if (!isset($changes["id"]))
      LogManager::error("Invalid student update parameters");

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
      $values[] = ($changes["tuition_enabled"]) ? 't' : 'f';
    }

    if (empty($fields))
      return;

    $values[] = $id;
    $sql = "UPDATE students SET " . implode(", ", $fields) . " WHERE id=$" . count($values);

    $result = pg_query_params(Model::getConn(), $sql, $values);

    if (!$result) LogManager::error("Query failed: " . Model::getError());
  }

  public function delete($id)
  {
    if (!isset($id))
      LogManager::error("Invalid student delete parameter");

    $result = pg_execute(
      Model::getConn(),
      "student_delete",
      [$id]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());
  }

  public function add(Student $student)
  {
    $result = pg_execute(
      Model::getConn(),
      "student_add",
      [$student->name, $student->surname, $student->email, $student->phone_number, (($student->tuition_enabled) ? "t" : "f")]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());
  }
}
