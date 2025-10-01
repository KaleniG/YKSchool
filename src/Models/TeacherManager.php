<?php

namespace App\Models;

use App\Config\LogManager;
use App\Config\Model;

class TeacherManager extends Model
{
  private static $prepared = false;

  public function prepareAll()
  {
    if (TeacherManager::$prepared) return;

    pg_prepare(
      Model::getConn(),
      "get_all_teachers_with_subjects",
      "SELECT 
      t.id AS id, 
      t.name AS name, 
      t.surname AS surname, 
      t.email AS email, 
      t.phone_number AS phone_number, 
      COALESCE(ARRAY_AGG(st.subject_id ORDER BY st.subject_id), '{}') AS teaching_subjects
      FROM teachers t
      LEFT JOIN subject_teachers st ON t.id = st.teacher_id
      GROUP BY t.id, t.name, t.surname, t.email, t.phone_number
      ORDER BY t.id"
    );

    pg_prepare(
      Model::getConn(),
      "get_teacher",
      "SELECT 
      t.id AS id, 
      t.name AS name, 
      t.surname AS surname, 
      t.email AS email, 
      t.phone_number AS phone_number, 
      COALESCE(ARRAY_AGG(st.subject_id ORDER BY st.subject_id), '{}') AS teaching_subjects
      FROM teachers t
      LEFT JOIN subject_teachers st ON t.id = st.teacher_id
      WHERE t.id=$1
      GROUP BY t.id, t.name, t.surname, t.email, t.phone_number"
    );

    pg_prepare(
      Model::getConn(),
      "get_teacher_courses",
      "SELECT c.id AS id, c.name AS name, c.description AS description
      FROM courses c 
      LEFT JOIN course_teachers ct ON ct.course_id = c.id
      WHERE ct.teacher_id=$1
      ORDER BY c.id"
    );

    pg_prepare(
      Model::getConn(),
      "delete_teacher_subject",
      "DELETE FROM subject_teachers WHERE subject_id=$1 AND teacher_id=$2"
    );

    pg_prepare(
      Model::getConn(),
      "add_teacher_subject",
      "INSERT INTO subject_teachers (subject_id, teacher_id) 
      VALUES ($1, $2)"
    );

    pg_prepare(
      Model::getConn(),
      "delete_teacher",
      "DELETE FROM teachers WHERE id=$1"
    );

    pg_prepare(
      Model::getConn(),
      "add_teacher",
      "INSERT INTO teachers (name, surname, email, phone_number) 
      VALUES ($1, $2, $3, $4) RETURNING id"
    );

    TeacherManager::$prepared = true;
  }

  public function validate(Teacher $teacher)
  {
    pg_prepare(
      Model::getConn(),
      "teacher_validate",
      "SELECT 
      t.id AS id, 
      t.name AS name, 
      t.surname AS surname, 
      t.email AS email, 
      t.phone_number AS phone_number, 
      COALESCE(ARRAY_AGG(st.subject_id ORDER BY st.subject_id), '{}') AS teaching_subjects
      FROM teachers t
      LEFT JOIN subject_teachers st ON t.id = st.teacher_id
      WHERE t.name=$1 AND t.surname=$2
      GROUP BY t.id, t.name, t.surname, t.email, t.phone_number"
    );

    $result = pg_execute(
      Model::getConn(),
      "teacher_validate",
      [$teacher->name, $teacher->surname]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    $user = pg_fetch_assoc($result, 0);

    if (isset($user) && !empty($user)) {
      $teacher->id = $user["id"];
      $teacher->name = $user["name"];
      $teacher->surname = $user["surname"];
      $teacher->email = $user["email"];
      $teacher->phone_number = $user["phone_number"];

      if (isset($user['teaching_subjects'])) {
        $pgArray = trim($user['teaching_subjects'], '{}');
        $teacher->teaching_subjects = ($pgArray === '') ? [] : array_map('intval', explode(',', $pgArray));
      }

      return $teacher;
    } else
      return false;
  }

  public function getTeacher($id)
  {
    $this->prepareAll();

    $result = pg_execute(
      Model::getConn(),
      "get_teacher",
      [$id]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    $teacher = pg_fetch_assoc($result, 0);

    if (isset($teacher) && !empty($teacher)) {
      if (isset($teacher["teaching_subjects"])) {
        $pgArray = trim($teacher["teaching_subjects"], '{}');
        $teacher["teaching_subjects"] = ($pgArray === '') ? [] : array_map('intval', explode(',', $pgArray));
      }

      return $teacher;
    } else
      return false;
  }

  public function getAllTeachersWithSubjects()
  {
    $this->prepareAll();

    $result = pg_execute(
      Model::getConn(),
      "get_all_teachers_with_subjects",
      []
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    $teachers = pg_fetch_all($result);

    if (!$teachers) return [];

    foreach ($teachers as &$teacher) {
      if (isset($teacher['teaching_subjects'])) {
        $pgArray = trim($teacher['teaching_subjects'], '{}');
        $teacher['teaching_subjects'] = $pgArray === '' ? [] : array_map('intval', explode(',', $pgArray));
      }
    }

    return $teachers;
  }

  public function getTeacherCourses($id)
  {
    $this->prepareAll();

    $result = pg_execute(
      Model::getConn(),
      "get_teacher_courses",
      [$id]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    return pg_fetch_all($result);
  }

  public function update($changes)
  {
    $this->prepareAll();

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

    $values[] = $id;
    $sql = "UPDATE teachers SET " . implode(", ", $fields) . " WHERE id=$" . count($values);

    $result = pg_query_params(Model::getConn(), $sql, $values);

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    $manager = new SubjectManager();
    $manager->prepareAll();
    $all_subjects = $manager->getTeacherSubjects($id) ?? [];

    // Ensure array
    $changes["teaching_subjects"] = isset($changes["teaching_subjects"])
      ? (array)$changes["teaching_subjects"]
      : [];

    foreach ($all_subjects as $subject) {
      $subjectId = $subject["id"];
      if (($key = array_search($subjectId, $changes["teaching_subjects"])) !== false) {
        unset($changes["teaching_subjects"][$key]);
      } else if (!in_array($subjectId, $changes["teaching_subjects"])) {
        $result = pg_execute(
          Model::getConn(),
          "delete_teacher_subject",
          [$subjectId, $id]
        );
        if (!$result) LogManager::error("Query failed: " . Model::getError());
      }
    }

    foreach ($changes["teaching_subjects"] as $subjectId) {
      $result = pg_execute(
        Model::getConn(),
        "add_teacher_subject",
        [$subjectId, $id]
      );
      if (!$result) LogManager::error("Query failed: " . Model::getError());
    }
  }

  public function delete($id)
  {
    $this->prepareAll();

    if (!isset($id))
      LogManager::error("Invalid subject delete parameters");

    $result = pg_execute(
      Model::getConn(),
      "delete_teacher",
      [$id]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());
  }

  public function add(Teacher $teacher)
  {
    $this->prepareAll();

    $result = pg_execute(
      Model::getConn(),
      "add_teacher",
      [$teacher->name, $teacher->surname, $teacher->email, $teacher->phone_number]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    $new_teacher_id = pg_fetch_assoc($result, 0);

    foreach ($teacher->teaching_subjects as $teaching_subject) {

      $result = pg_execute(
        Model::getConn(),
        "add_teacher_subject",
        [$teaching_subject, $new_teacher_id["id"]]
      );

      if (!$result) LogManager::error("Query failed: " . Model::getError());
    }
  }
}
