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
    $query = "SELECT COUNT(*) FROM teachers WHERE name = $1 AND surname = $2";
    $result = pg_query_params(Model::getConn(), $query, array($teacher->name, $teacher->surname));

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    $count = pg_fetch_result($result, 0, 0);
    return $count > 0;
  }

  public function getTeacher($name, $surname)
  {
    pg_prepare(
      Model::getConn(),
      "get_{$name}_{$surname}_teacher",
      "SELECT 
        t.id AS teacher_id,
        t.email AS teacher_email, 
        t.phone_number AS teacher_phone_number, 
        ARRAY_AGG(s.subject) AS teacher_subjects
     FROM teachers t
     LEFT JOIN subject_teachers st ON t.id = st.teacher_id
     LEFT JOIN subjects s ON st.subject_id = s.id
     WHERE t.name = $1 AND t.surname = $2
     GROUP BY t.id, t.email, t.phone_number"
    );

    $result = pg_execute(Model::getConn(), "get_{$name}_{$surname}_teacher", [$name, $surname]);
    if (!$result) {
      LogManager::error("Query failed: " . Model::getError());
    }

    $row = pg_fetch_assoc($result);

    if (!$row) {
      return null; // teacher not found
    }

    // teacher_subjects is a Postgres array literal or NULL
    $teacher_subjects_string = $row['teacher_subjects'] ?? '{}';

    // convert to PHP array:
    $teacher_subjects_array = str_getcsv(trim($teacher_subjects_string, '{}'));

    return [
      "id" => $row['teacher_id'],
      "name" => $name,
      "surname" => $surname,
      "email" => $row['teacher_email'],
      "phone_number" => $row['teacher_phone_number'],
      "teaching_subjects" => $teacher_subjects_array
    ];
  }

  public function getAllTeachersWithSubjects()
  {
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

  public function getAllSubjectTeachers($subject_id)
  {
    pg_prepare(
      Model::getConn(),
      "get_subject_teachers",
      "SELECT 
            t.id AS teacher_id, 
            t.name AS teacher_name, 
            t.surname AS teacher_surname
         FROM teachers t
         LEFT JOIN subject_teachers st 
            ON t.id = st.teacher_id
         WHERE st.subject_id = $1"
    );

    $result = pg_execute(Model::getConn(), "get_subject_teachers", [$subject_id]);
    if (!$result) {
      LogManager::error("Query failed: " . Model::getError());
    }

    return pg_fetch_all($result) ?: [];
  }

  public function getAllTeacherSubjects()
  {
    $query = "SELECT t.id AS teacher_id, STRING_AGG(s.subject, ',') AS subjects
    FROM teachers t
    LEFT JOIN subject_teachers st ON t.id = st.teacher_id
    LEFT JOIN subjects s ON st.subject_id = s.id
    GROUP BY t.id, t.name, t.surname";

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
      $email = htmlspecialchars($fields['email']);
      $phone = htmlspecialchars($fields['phone_number']);
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
