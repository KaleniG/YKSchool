<?php

namespace App\Models;

use App\Config\LogManager;
use App\Config\Model;

class StudentManager extends Model
{
  public function validate(Student $admin)
  {
    $query = "SELECT COUNT(*) FROM students WHERE name = $1 AND surname = $2";
    $result = pg_query_params(Model::getConn(), $query, array($admin->name, $admin->surname));

    if (!$result) LogManager::error("Query failed");

    $count = pg_fetch_result($result, 0, 0);
    return $count > 0;
  }

  public function getAllStudents()
  {
    $query = "SELECT * FROM students";
    $result = pg_query(Model::getConn(), $query);

    if (!$result)
      LogManager::error("Query failed");

    return pg_fetch_all($result);
  }

  public function getStudent($name, $surname)
  {
    pg_prepare(
      Model::getConn(),
      "get_student",
      "SELECT * FROM students WHERE students.name=$1 AND students.surname=$2 LIMIT 1"
    );

    $result = pg_execute(Model::getConn(), "get_student", array($name, $surname));
    if (!$result) LogManager::error("Query failed");

    $row = pg_fetch_assoc($result);
    return [
      "id" => $row['id'],
      "name" => $row['name'],
      "surname" => $row['surname'],
      "email" => $row['email'],
      "phone_number" => $row['phone_number'],
      "tuition_enabled" => $row['tuition_enabled']
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

  public function delete($id)
  {
    pg_prepare(Model::getConn(), "students_delete", "DELETE FROM students WHERE id=$1");
    pg_execute(Model::getConn(), "students_delete", array($id));
  }

  public function add(Student $student)
  {
    pg_prepare(Model::getConn(), "students_add", "INSERT INTO students (name, surname, email, phone_number, tuition_enabled) VALUES ($1, $2, $3, $4, $5)");
    pg_execute(Model::getConn(), "students_add", array($student->name, $student->surname, $student->email, $student->phone_number, $student->tuition_enabled));
  }
}
