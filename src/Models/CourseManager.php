<?php

namespace App\Models;

use App\Config\LogManager;
use App\Config\Model;

class CourseManager extends Model
{

  public function getAllCourses()
  {
    $query = "SELECT * FROM courses";
    $result = pg_query(Model::getConn(), $query);

    if (!$result)
      LogManager::error("Query failed");

    return pg_fetch_all($result);
  }

  public function getAllCourseTeachers()
  {
    $query = "SELECT c.id AS course_id, STRING_AGG(t.name || ' ' || t.surname, ', ') AS teachers
              FROM courses c
              LEFT JOIN course_teachers ct ON c.id = ct.course_id
              LEFT JOIN teachers t ON ct.teacher_id = t.id
              GROUP BY c.id";
    $result = pg_query(Model::getConn(), $query);

    if (!$result) LogManager::error('Query failed: ' . Model::getError());

    return pg_fetch_all($result);
  }

  public function getAllCourseStudents()
  {
    $query = "SELECT c.id AS course_id, STRING_AGG(s.name || ' ' || s.surname, ', ') AS students
              FROM courses c
              LEFT JOIN course_students cs ON c.id = cs.course_id
              LEFT JOIN students s ON cs.student_id = s.id
              GROUP BY c.id";
    $result = pg_query(Model::getConn(), $query);

    if (!$result) LogManager::error('Query failed: ' . Model::getError());

    return pg_fetch_all($result);
  }

  public function updateChanges($changes)
  {
    pg_prepare(
      Model::getConn(),
      "course_update",
      "UPDATE courses SET name=$1, description=$2, status=$3, subject_id=$4 WHERE id=$5"
    );
    foreach ($changes as $id => $fields) {
      $email = $fields['email'];
      $phone = $fields['phone_number'];

      LogManager::info("$id, $email, $phone");
      pg_execute(Model::getConn(), "course_update", array($email, $phone, $id));
    }
  }

  public function delete($id)
  {
    pg_prepare(Model::getConn(), "course_delete", "DELETE FROM administrators WHERE id=$1");
    pg_execute(Model::getConn(), "course_delete", array($id));
  }

  public function add(Course $course)
  {
    pg_prepare(Model::getConn(), "course_add", "INSERT INTO courses (name, description, status, subject_id) VALUES ($1, $2, $3, $4)");
    pg_execute(Model::getConn(), "course_add", array($course->name, $course->description, $course->status, $course->subject));
  }
}
