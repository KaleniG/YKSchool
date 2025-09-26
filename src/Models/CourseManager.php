<?php

namespace App\Models;

use App\Config\LogManager;
use App\Config\Model;

class CourseManager extends Model
{
  private static $prepared = false;

  public function prepareAll()
  {
    if (CourseManager::$prepared) return;

    pg_prepare(
      Model::getConn(),
      "get_all_courses_with_details",
      "SELECT 
      c.id AS id, 
      c.name AS name, 
      c.description AS description, 
      c.status AS status, 
      c.subject_id AS subject, 
      COALESCE(ARRAY_AGG(ct.teacher_id ORDER BY ct.teacher_id), '{}') AS course_teachers,
      COALESCE(ARRAY_AGG(cs.student_id ORDER BY cs.student_id), '{}') AS course_students
      FROM courses c 
      LEFT JOIN course_teachers ct ON ct.course_id = c.id
      LEFT JOIN course_students cs On cs.course_id = c.id
      GROUP BY c.id, c.name, c.description, c.subject_id, c.status
      ORDER BY c.id"
    );

    pg_prepare(
      Model::getConn(),
      "get_all_course_teachers",
      "SELECT * FROM course_teachers WHERE course_id = $1"
    );

    pg_prepare(
      Model::getConn(),
      "get_all_course_students",
      "SELECT * FROM course_students WHERE course_id = $1"
    );

    pg_prepare(
      Model::getConn(),
      "delete_course",
      "DELETE FROM courses 
      WHERE id=$1"
    );

    pg_prepare(
      Model::getConn(),
      "add_course",
      "INSERT INTO courses (name, description, status, subject_id) VALUES ($1, $2, $3, $4) RETURNING id"
    );

    pg_prepare(
      Model::getConn(),
      "add_course_teacher",
      "INSERT INTO course_teachers (course_id, teacher_id) VALUES ($1, $2)"
    );

    pg_prepare(
      Model::getConn(),
      "add_course_student",
      "INSERT INTO course_students (course_id, student_id) VALUES ($1, $2)"
    );

    pg_prepare(
      Model::getConn(),
      "delete_course_teacher",
      "DELETE FROM course_teachers WHERE course_id=$1 AND teacher_id=$2"
    );

    pg_prepare(
      Model::getConn(),
      "delete_course_student",
      "DELETE FROM course_students WHERE course_id=$1 AND student_id=$2"
    );

    CourseManager::$prepared = true;
  }

  public function getAllCourses()
  {
    $result = pg_execute(
      Model::getConn(),
      "get_all_admins",
      []
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    return pg_fetch_all($result);

    $query = "SELECT * FROM courses";
    $result = pg_query(Model::getConn(), $query);

    if (!$result)
      LogManager::error("Query failed");

    return pg_fetch_all($result);
  }

  public function getAllCoursesWithDetails()
  {
    $result = pg_execute(
      Model::getConn(),
      "get_all_courses_with_details",
      []
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    $courses = pg_fetch_all($result);

    if (!$courses) return [];

    foreach ($courses as &$course) {
      if (isset($course['course_teachers'])) {
        $pgArray = trim($course['course_teachers'], '{}');
        $course['course_teachers'] = $pgArray === '' ? [] : array_map('intval', explode(',', $pgArray));
      }
      if (isset($course['course_students'])) {
        $pgArray = trim($course['course_students'], '{}');
        $course['course_students'] = $pgArray === '' ? [] : array_map('intval', explode(',', $pgArray));
      }
    }

    return $courses;
  }

  /*
  public function getAllCourseTeachers()
  {
    $query = "SELECT * FROM course_teachers";
    $result = pg_query(Model::getConn(), $query);

    if (!$result) LogManager::error('Query failed: ' . Model::getError());

    return pg_fetch_all($result);
  }*/

  public function getAllCourseTeachers($id)
  {
    if (!isset($id))
      LogManager::error("Invalid course delete parameter");

    $result = pg_execute(
      Model::getConn(),
      "get_all_course_teachers",
      [$id]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    return pg_fetch_all($result);
  }

  public function getAllCourseStudents($id)
  {
    if (!isset($id))
      LogManager::error("Invalid course delete parameter");

    $result = pg_execute(
      Model::getConn(),
      "get_all_course_students",
      [$id]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    return pg_fetch_all($result);
  }

  public function getCoursesOfTeacher($teacher_id)
  {
    pg_prepare(
      Model::getConn(),
      "get_courses_of_teacher",
      "SELECT * FROM course_teachers JOIN courses ON course_teachers.course_id = courses.id
      WHERE course_teachers.teacher_id = $1"
    );

    $result = pg_execute(Model::getConn(), "get_courses_of_teacher", array($teacher_id));
    if (!$result) LogManager::error("Query failed: " . Model::getError());
    return pg_fetch_all($result);
  }

  public function getCoursesOfStudent($student_id)
  {
    pg_prepare(
      Model::getConn(),
      "get_courses_of_student",
      "SELECT courses.id AS course_id, courses.name AS course_name, courses.description AS course_description
      FROM course_students JOIN courses ON course_students.course_id = courses.id
      WHERE course_students.student_id = $1"
    );

    $result = pg_execute(Model::getConn(), "get_courses_of_student", array($student_id));
    if (!$result) LogManager::error("Query failed: " . Model::getError());
    return pg_fetch_all($result);
  }

  /*
  public function getAllCourseStudents()
  {
    $query = "SELECT * FROM course_students";
    $result = pg_query(Model::getConn(), $query);

    if (!$result) LogManager::error('Query failed: ' . Model::getError());

    return pg_fetch_all($result);
  }*/

  public function updateChanges($changes)
  {
    pg_prepare(
      Model::getConn(),
      "course_update",
      "UPDATE courses SET name=$1, description=$2, status=$3 WHERE id=$4"
    );
    foreach ($changes as $id => $fields) {
      $name = htmlspecialchars($fields['name']);
      $description = htmlspecialchars($fields['description']);
      $status = htmlspecialchars($fields['status']);

      $result = pg_execute(Model::getConn(), "course_update", array($name, $description, $status, $id));
      if (!$result) LogManager::error('Query failed: ' . Model::getError());

      // Course teachers update logic
      pg_prepare(
        Model::getConn(),
        "course_teachers_query",
        "SELECT teacher_id FROM course_teachers WHERE course_teachers.course_id=$1"
      );
      $result = pg_execute(Model::getConn(), "course_teachers_query", array($id));
      if (!$result) LogManager::error("Query failed: " . Model::getError());
      $teachers = pg_fetch_all($result);

      if (!isset($fields["teachers"]))
        $fields["teachers"] = [];

      foreach ($teachers as $teacher) {
        if (($where = array_search($teacher["teacher_id"], $fields["teachers"])) !== false) {
          unset($fields["teachers"][$where]);
        } else if (!in_array($teacher["teacher_id"], $fields["teachers"])) {
          pg_prepare(
            Model::getConn(),
            "course_teachers_delete_obsolete",
            "DELETE FROM course_teachers WHERE teacher_id=$1 AND course_id=$2"
          );
          $result = pg_execute(Model::getConn(), "course_teachers_delete_obsolete", array($teacher["teacher_id"], $id));
          if (!$result) LogManager::error("Query failed: " . Model::getError());
        }
      }
      foreach ($fields["teachers"] as $changed_teacher) {
        pg_prepare(Model::getConn(), "course_teacher_add", "INSERT INTO course_teachers (course_id, teacher_id) VALUES ($1, $2) ON CONFLICT DO NOTHING");
        $result = pg_execute(Model::getConn(), "course_teacher_add", [$id, $changed_teacher]);
        if (!$result) LogManager::error("Query failed: " . Model::getError());
      }

      // Course students update logic
      pg_prepare(
        Model::getConn(),
        "course_students_query",
        "SELECT student_id FROM course_students WHERE course_students.course_id=$1"
      );
      $result = pg_execute(Model::getConn(), "course_students_query", array($id));
      if (!$result) LogManager::error("Query failed: " . Model::getError());
      $students = pg_fetch_all($result);

      if (!isset($fields["students"]))
        $fields["students"] = [];

      foreach ($students as $student) {
        if (($where = array_search($student["student_id"], $fields["students"])) !== false) {
          unset($fields["students"][$where]);
        } else if (!in_array($student["student_id"], $fields["students"])) {
          pg_prepare(
            Model::getConn(),
            "course_students_delete_obsolete",
            "DELETE FROM course_students WHERE student_id=$1 AND course_id=$2"
          );
          $result = pg_execute(Model::getConn(), "course_students_delete_obsolete", array($student["student_id"], $id));
          if (!$result) LogManager::error("Query failed: " . Model::getError());
        }
      }
      foreach ($fields["students"] as $changed_student) {
        pg_prepare(Model::getConn(), "course_student_add", "INSERT INTO course_students (course_id, student_id) VALUES ($1, $2) ON CONFLICT DO NOTHING");
        $result = pg_execute(Model::getConn(), "course_student_add", [$id, $changed_student]);
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

    if (array_key_exists("name", $changes)) {
      $fields[] = "name = $" . (count($values) + 1);
      $values[] = $changes["name"];
    }
    if (array_key_exists("description", $changes)) {
      $fields[] = "description = $" . (count($values) + 1);
      $values[] = $changes["description"];
    }
    if (array_key_exists("status", $changes)) {
      $fields[] = "status = $" . (count($values) + 1);
      $values[] = $changes["status"];
    }

    $values[] = $id;
    $sql = "UPDATE courses SET " . implode(", ", $fields) . " WHERE id=$" . count($values);

    $result = pg_query_params(Model::getConn(), $sql, $values);

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    $all_teachers = $this->getAllCourseTeachers($id) ?? [];

    // Ensure array
    $changes["course_teachers"] = isset($changes["course_teachers"]) ? (array)$changes["course_teachers"] : [];

    foreach ($all_teachers as $teacher) {
      $teacherId = $teacher["id"];
      if (($key = array_search($teacherId, $changes["course_teachers"])) !== false) {
        unset($changes["course_teachers"][$key]);
      } else if (!in_array($teacherId, $changes["course_teachers"])) {
        $result = pg_execute(
          Model::getConn(),
          "delete_course_teacher",
          [$id, $teacherId]
        );
        if (!$result) LogManager::error("Query failed: " . Model::getError());
      }
    }

    foreach ($changes["course_teachers"] as $teacherId) {
      $result = pg_execute(
        Model::getConn(),
        "add_course_teacher",
        [$id, $teacherId]
      );
      if (!$result) LogManager::error("Query failed: " . Model::getError());
    }

    $all_students = $this->getAllCourseStudents($id) ?? [];

    // Ensure array
    $changes["course_students"] = isset($changes["course_students"]) ? (array)$changes["course_students"] : [];

    foreach ($all_students as $student) {
      $studentId = $student["id"];
      if (($key = array_search($studentId, $changes["course_students"])) !== false) {
        unset($changes["course_students"][$key]);
      } else if (!in_array($studentId, $changes["course_students"])) {
        $result = pg_execute(
          Model::getConn(),
          "delete_course_student",
          [$id, $studentId]
        );
        if (!$result) LogManager::error("Query failed: " . Model::getError());
      }
    }

    foreach ($changes["course_students"] as $studentId) {
      $result = pg_execute(
        Model::getConn(),
        "add_course_student",
        [$id, $studentId]
      );
      if (!$result) LogManager::error("Query failed: " . Model::getError());
    }
  }

  public function updateDescription($changes)
  {
    pg_prepare(
      Model::getConn(),
      "course_update",
      "UPDATE courses SET description=$1 WHERE id=$2"
    );
    foreach ($changes as $id => $fields) {
      $description = htmlspecialchars($fields['description']);

      $result = pg_execute(Model::getConn(), "course_update", array($description, $id));
      if (!$result) LogManager::error('Query failed: ' . Model::getError());
    }
  }

  public function updateUser($changes, $student_id)
  {
    $courses = $this->getCoursesOfStudent($student_id);

    $current_user_course_ids = [];
    foreach ($courses as $course)
      array_push($current_user_course_ids, $course["course_id"]);

    foreach ($changes as $id => $user_id) {
      if (($where = array_search($id, $current_user_course_ids)) !== false) {
        unset($current_user_course_ids[$where]);
        continue;
      } else if (!in_array($id, $current_user_course_ids)) {
        pg_prepare(
          Model::getConn(),
          "insert_course_user_student",
          "INSERT INTO course_students (course_id, student_id) VALUES ($1, $2)"
        );

        $result = pg_execute(Model::getConn(), "insert_course_user_student", array($id, $student_id));
        if (!$result) LogManager::info("Query failed: " . Model::getError());
        continue;
      }
    }

    foreach ($current_user_course_ids as $deleted_course) {
      pg_prepare(Model::getConn(), "delete_student_course", "DELETE FROM course_students WHERE course_id=$1 AND student_id=$2");
      $result = pg_execute(Model::getConn(), "delete_student_course", [$deleted_course, $student_id]);
      if (!$result) LogManager::error("Query failed: " . Model::getError());
    }
  }

  public function delete($id)
  {
    if (!isset($id))
      LogManager::error("Invalid course delete parameter");

    $result = pg_execute(
      Model::getConn(),
      "delete_course",
      [$id]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());
  }

  public function add(Course $course)
  {
    $result = pg_execute(
      Model::getConn(),
      "add_course",
      [$course->name, $course->description, $course->status, $course->subject]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    $new_course_id = pg_fetch_assoc($result, 0);

    foreach ($course->teachers as $teacher_id) {
      $result = pg_execute(
        Model::getConn(),
        "add_course_teacher",
        [$new_course_id["id"], $teacher_id]
      );

      if (!$result) LogManager::error("Query failed: " . Model::getError());
    }

    foreach ($course->students as $student_id) {
      $result = pg_execute(
        Model::getConn(),
        "add_course_student",
        [$new_course_id["id"], $student_id]
      );

      if (!$result) LogManager::error("Query failed: " . Model::getError());
    }
  }
}
