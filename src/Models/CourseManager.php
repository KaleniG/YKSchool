<?php

namespace App\Models;

use App\Config\Log;
use App\Config\Model;

class CourseManager extends Model
{
  private static $prepared = false;

  public function prepareAll()
  {
    if (CourseManager::$prepared) return;

    pg_prepare(
      Model::getConn(),
      "get_all_courses",
      "SELECT * FROM courses"
    );

    pg_prepare(
      Model::getConn(),
      "get_all_courses_with_face_value",
      "SELECT 
      c.name AS name, 
      c.description AS description, 
      s.name AS subject
      FROM courses c
      LEFT JOIN subjects s ON c.subject_id = s.id
      WHERE c.status = 'Active' 
      ORDER BY c.id"
    );

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
      "get_all_courses_on_student",
      "SELECT 
      c.id AS id,
      c.name AS name,
      c.description AS description,
      EXISTS (
        SELECT 1
        FROM course_students cs
        WHERE cs.course_id = c.id AND cs.student_id = $1
      ) AS is_student_subscribed
      FROM courses c"
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
      "INSERT INTO course_teachers (course_id, teacher_id) VALUES ($1, $2) ON CONFLICT DO NOTHING"
    );

    pg_prepare(
      Model::getConn(),
      "add_course_student",
      "INSERT INTO course_students (course_id, student_id) VALUES ($1, $2) ON CONFLICT DO NOTHING"
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

    pg_prepare(
      Model::getConn(),
      "update_course_description",
      "UPDATE courses SET description=$1 WHERE id=$2"
    );

    CourseManager::$prepared = true;
  }

  public function getAllCourses()
  {
    $this->prepareAll();

    $result = pg_execute(
      Model::getConn(),
      "get_all_courses",
      []
    );

    if (!$result) Log::error("Query failed: " . Model::getError());

    return pg_fetch_all($result);
  }

  public function getAllCoursesWithFaceValue()
  {
    $this->prepareAll();

    $result = pg_execute(
      Model::getConn(),
      "get_all_courses_with_face_value",
      []
    );

    if (!$result) Log::error("Query failed: " . Model::getError());

    return pg_fetch_all($result);
  }

  public function getAllCoursesWithFilter($word_filter, $subject_filter)
  {
    if (empty($word_filter) && empty($subject_filter))
      return;

    $conditions = [];
    $values = [];

    if (!empty($subject_filter)) {
      $conditions[] = "c.subject_id = $" . (count($values) + 1);
      $values[] = $subject_filter;
    }

    if (!empty($word_filter)) {
      $paramIndex = count($values) + 1;
      $conditions[] = "(c.name ILIKE $" . $paramIndex . " OR c.description ILIKE $" . $paramIndex . ")";
      $values[] = "%" . $word_filter . "%";
    }

    $sql = "SELECT 
    c.name AS name, 
    c.description AS description, 
    s.name AS subject 
    FROM courses c
    LEFT JOIN subjects s ON c.subject_id = s.id
    WHERE " . implode(" AND ", $conditions) . " AND c.status = 'Active' 
    ORDER BY c.id";

    $result = pg_query_params(Model::getConn(), $sql, $values);

    if (!$result) Log::error("Query failed: " . Model::getError());

    return pg_fetch_all($result);
  }

  public function getAllCoursesWithDetails()
  {
    $this->prepareAll();

    $result = pg_execute(
      Model::getConn(),
      "get_all_courses_with_details",
      []
    );

    if (!$result) Log::error("Query failed: " . Model::getError());

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

  public function getAllCoursesOnStudent($id)
  {
    $this->prepareAll();

    $result = pg_execute(
      Model::getConn(),
      "get_all_courses_on_student",
      [$id]
    );

    if (!$result) Log::error("Query failed: " . Model::getError());

    $courses = pg_fetch_all($result);

    if (!$courses) return [];

    foreach ($courses as &$course)
      $course["is_student_subscribed"] = ($course["is_student_subscribed"] == "t") ? true : false;

    return $courses;
  }

  public function getAllCourseTeachers($id)
  {
    $this->prepareAll();

    if (!isset($id))
      Log::error("Invalid course delete parameter");

    $result = pg_execute(
      Model::getConn(),
      "get_all_course_teachers",
      [$id]
    );

    if (!$result) Log::error("Query failed: " . Model::getError());

    return pg_fetch_all($result);
  }

  public function getAllCourseStudents($id)
  {
    $this->prepareAll();

    if (!isset($id))
      Log::error("Invalid course delete parameter");

    $result = pg_execute(
      Model::getConn(),
      "get_all_course_students",
      [$id]
    );

    if (!$result) Log::error("Query failed: " . Model::getError());

    return pg_fetch_all($result);
  }

  public function update($changes)
  {
    $this->prepareAll();

    if (!isset($changes["id"]))
      Log::error("Invalid student update parameters");

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

    if (!$result) Log::error("Query failed: " . Model::getError());

    $all_teachers = $this->getAllCourseTeachers($id) ?? [];

    // Ensure array
    $changes["course_teachers"] = isset($changes["course_teachers"]) ? (array)$changes["course_teachers"] : [];

    foreach ($all_teachers as $teacher) {
      $teacherId = $teacher["teacher_id"];
      if (!in_array($teacherId, $changes["course_teachers"])) {
        $result = pg_execute(
          Model::getConn(),
          "delete_course_teacher",
          [$id, $teacherId]
        );

        if (!$result) Log::error("Query failed: " . Model::getError());
      } else {
        $key = array_search($teacherId, $changes["course_teachers"]);
        unset($changes["course_teachers"][$key]);
      }
    }

    foreach ($changes["course_teachers"] as $teacherId) {
      $result = pg_execute(
        Model::getConn(),
        "add_course_teacher",
        [$id, $teacherId]
      );

      if (!$result) Log::error("Query failed: " . Model::getError());
    }

    $all_students = $this->getAllCourseStudents($id) ?? [];
    $changes["course_students"] = isset($changes["course_students"]) ? (array)$changes["course_students"] : [];

    foreach ($all_students as $student) {
      $studentId = $student["student_id"];
      if (!in_array($studentId, $changes["course_students"])) {
        $result = pg_execute(
          Model::getConn(),
          "delete_course_student",
          [$id, $studentId]
        );

        if (!$result) Log::error("Query failed: " . Model::getError());
      } else {
        $key = array_search($studentId, $changes["course_students"]);
        unset($changes["course_students"][$key]);
      }
    }

    foreach ($changes["course_students"] as $studentId) {
      $result = pg_execute(
        Model::getConn(),
        "add_course_student",
        [$id, $studentId]
      );

      if (!$result) Log::error("Query failed: " . Model::getError());
    }
  }

  public function updateDescription($changes)
  {
    $this->prepareAll();

    if (!isset($changes["id"]))
      Log::error("Invalid course update parameter");

    $id = htmlspecialchars($changes['id']);
    $description = htmlspecialchars($changes['description']) ?? "";

    if (!isset($description))
      Log::error("Invalid course update parameter");

    $result = pg_execute(
      Model::getConn(),
      "update_course_description",
      [$description, $id]
    );

    if (!$result) Log::error('Query failed: ' . Model::getError());
  }

  public function updateCourseSubscription($changes)
  {
    $this->prepareAll();

    if (!isset($changes["student_id"], $changes["course_id"]))
      Log::error("Invalid course update parameter");

    $student_id = $changes['student_id'];
    $course_id = $changes['course_id'];
    $is_student_subscribed = $changes['is_student_subscribed'] ?? false;

    if ($is_student_subscribed) {
      $result = pg_execute(
        Model::getConn(),
        "add_course_student",
        [$course_id, $student_id]
      );

      if (!$result) Log::error("Query failed: " . Model::getError());
    } else {
      $result = pg_execute(
        Model::getConn(),
        "delete_course_student",
        [$course_id, $student_id]
      );

      if (!$result) Log::error("Query failed: " . Model::getError());
    }
  }

  public function delete($id)
  {
    $this->prepareAll();

    if (!isset($id))
      Log::error("Invalid course delete parameter");

    $result = pg_execute(
      Model::getConn(),
      "delete_course",
      [$id]
    );

    if (!$result) Log::error("Query failed: " . Model::getError());
  }

  public function add(Course $course)
  {
    $this->prepareAll();

    $result = pg_execute(
      Model::getConn(),
      "add_course",
      [$course->name, $course->description, $course->status, $course->subject]
    );

    if (!$result) Log::error("Query failed: " . Model::getError());

    $new_course_id = pg_fetch_assoc($result, 0);

    foreach ($course->teachers as $teacher_id) {
      $result = pg_execute(
        Model::getConn(),
        "add_course_teacher",
        [$new_course_id["id"], $teacher_id]
      );

      if (!$result) Log::error("Query failed: " . Model::getError());
    }

    foreach ($course->students as $student_id) {
      $result = pg_execute(
        Model::getConn(),
        "add_course_student",
        [$new_course_id["id"], $student_id]
      );

      if (!$result) Log::error("Query failed: " . Model::getError());
    }
  }
}
