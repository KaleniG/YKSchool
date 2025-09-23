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

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    $count = pg_fetch_result($result, 0, 0);
    return $count > 0;
  }

  public function getAllTeachers()
  {
    $query = "SELECT * FROM teachers";
    $result = pg_query(Model::getConn(), $query);

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    return pg_fetch_all($result);
  }

  public function getAllSubjectTeachers($subject_id)
  {
    pg_prepare(
      Model::getConn(),
      "get_subject_teachers",
      "SELECT *
      FROM teachers t
      LEFT JOIN subject_teachers st ON t.id = st.teacher_id
      WHERE st.subject_id = $1"
    );

    $result = pg_execute(Model::getConn(), "get_subject_teachers", array($subject_id));
    if (!$result) LogManager::error("Query failed: " . Model::getError());
    return pg_fetch_all($result);
  }

  public function getAllTeacherSubjects()
  {
    $query = "SELECT t.id AS teacher_id, STRING_AGG(s.subject, ',') AS subjects
    FROM teachers t
    LEFT JOIN subject_teachers st ON t.id = st.teacher_id
    LEFT JOIN subjects s ON st.subject_id = s.id";

    if (isset($subject) && !empty($subject)) {
      $query .= " WHERE s.id = " . $subject;
    }

    $query .= " GROUP BY t.id, t.name, t.surname";

    $result = pg_query(Model::getConn(), $query);

    if (!$result) LogManager::error("Query failed: " . Model::getError());

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
      $result = pg_execute(Model::getConn(), "teachers_update", array($email, $phone, $id));
      if (!$result) LogManager::error("Query failed: " . Model::getError());

      // Teacher subjects handling
      pg_prepare(
        Model::getConn(),
        "teacher_subjects_query",
        "SELECT subject_id FROM subject_teachers WHERE subject_teachers.teacher_id=$1"
      );
      $result = pg_execute(Model::getConn(), "teacher_subjects_query", array($id));
      if (!$result) LogManager::error("Query failed: " . Model::getError());
      $subjects = pg_fetch_all($result);

      if (!isset($fields["teaching_subjects"]))
        $fields["teaching_subjects"] = [];

      foreach ($subjects as $subject) {
        if (($where = array_search($subject["subject_id"], $fields["teaching_subjects"])) !== false) {
          unset($fields["teaching_subjects"][$where]);
        } else if (!in_array($subject["subject_id"], $fields["teaching_subjects"])) {
          pg_prepare(
            Model::getConn(),
            "teacher_subjects_delete_obsolete",
            "DELETE FROM subject_teachers WHERE teacher_id=$1 AND subject_id=$2"
          );
          $result = pg_execute(Model::getConn(), "teacher_subjects_delete_obsolete", array($id, $subject["subject_id"]));
          if (!$result) LogManager::info("Query failed: " . Model::getError());

          continue;
        }
      }
      foreach ($fields["teaching_subjects"] as $changed_teaching_subject) {
        pg_prepare(Model::getConn(), "teacher_subjects_add", "INSERT INTO subject_teachers (subject_id, teacher_id) VALUES ($1, $2) ON CONFLICT DO NOTHING");
        $result = pg_execute(Model::getConn(), "teacher_subjects_add", [$changed_teaching_subject, $id]);
        if (!$result) LogManager::error("Query failed: " . Model::getError());
      }
    }
  }

  public function delete($id)
  {
    pg_prepare(Model::getConn(), "teachers_delete", "DELETE FROM teachers WHERE id=$1");
    $result = pg_execute(Model::getConn(), "teachers_delete", array($id));
    if (!$result) LogManager::error("Query failed: " . Model::getError());
  }

  public function add(Teacher $teacher)
  {
    pg_prepare(Model::getConn(), "teachers_add", "INSERT INTO teachers (name, surname, email, phone_number) VALUES ($1, $2, $3, $4) RETURNING id");
    $result = pg_execute(Model::getConn(), "teachers_add", array($teacher->name, $teacher->surname, $teacher->email, $teacher->phone_number));
    if (!$result) LogManager::error("Query failed: " . Model::getError());
    $new_teacher_id = pg_fetch_result($result, 0, 'id');

    foreach ($teacher->teaching_subjects as $subject_name) {
      pg_prepare(Model::getConn(), "add_subject_teacher", "INSERT INTO subject_teachers (subject_id, teacher_id) VALUES ($1, $2) ON CONFLICT DO NOTHING");
      $result = pg_execute(Model::getConn(), "add_subject_teacher", [$subject_name, $new_teacher_id]);
      if (!$result) LogManager::error("Query failed: " . Model::getError());
    }
  }
}
