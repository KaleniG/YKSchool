<?php

namespace App\Controllers;

use App\Config\LogManager;
use App\Config\Path;
use App\Models\Admin;
use App\Models\AdminManager;
use App\Models\Course;
use App\Models\CourseManager;
use App\Models\Student;
use App\Models\StudentManager;
use App\Models\Subject;
use App\Models\SubjectManager;
use App\Models\Teacher;
use App\Models\TeacherManager;

class MainController
{
  private $page = null;
  private $user = null;
  private $edit_selection = null;
  private $current_table = [
    "admins" => [],
    "teachers" => [],
    "students" => [],
    "subjects" => [],
    "courses" => [],
    "subject_teachers" => [],
    "course_teachers" => [],
    "course_students" => [],
    "" => []
  ];
  private $temp = [];

  public function handleRequests()
  {
    $defaultPage = "Home.php";

    if (isset($_POST["page"]) && $_SERVER["REQUEST_METHOD"] == "POST")
      $_SESSION["page"] = $_POST["page"];

    $this->page = $_SESSION["page"] ?? $defaultPage;
    if (isset($_SESSION["user"]))
      $this->user = unserialize($_SESSION["user"]) ?? null;
    $this->edit_selection = $_SESSION["edit_selection"] ?? null;
    $this->current_table = $_SESSION["current_table"] ?? [
      "admins" => [],
      "teachers" => [],
      "students" => [],
      "subjects" => [],
      "courses" => [],
      "subject_teachers" => [],
      "course_teachers" => [],
      "course_students" => [],
      "" => []
    ];
    $this->temp = $_SESSION["temp"] ?? [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      switch ($this->page) {
        case "Admin/Login.php":
        case "Admin/Edit.php":
          $this->handleAdminClient();
          break;
        case "Teacher/Login.php":
        case "Teacher/Edit.php":
          $this->handleTeacherClient();
          break;
        case "Student/Login.php":
        case "Student/Edit.php":
          $this->handleStudentClient();
          break;
      }

      header("Location: index.php");
      exit;
    }

    include Path::views($this->page);
  }

  public function handleAdminClient()
  {
    switch ($this->page) {
      case "Admin/Login.php": {
          $manager = new AdminManager();
          $admin = new Admin();
          $admin->name = htmlspecialchars($_POST["name"] ?? "");
          $admin->surname = htmlspecialchars($_POST["surname"] ?? "");
          if ($manager->validate($admin)) {
            $this->user = serialize($admin);
            $_SESSION["user"] = $this->user;
            $this->page = "Admin/Edit.php";
            $_SESSION["page"] = $this->page;
          }
          break;
        }
      case "Admin/Edit.php": {
          // LOG OUT CASE
          if (isset($_POST["logout"])) {
            session_unset();
            break;
          }

          // PRESSED SELECT ON ADMINISTRATOR EDITING CHOICES
          $this->edit_selection = $_POST["edit_selection"] ?? null;
          $_SESSION["edit_selection"] = $this->edit_selection;

          // HANDLING ALL EDITING LOGIC
          if (isset($this->edit_selection)) {
            switch ($this->edit_selection) {
              case "admins":
                $this->handleAdminAdmins();
                break;
              case "teachers":
                $this->handleAdminTeachers();
                break;
              case "students":
                $this->handleAdminStudents();
                break;
              case "subjects":
                $this->handleAdminSubjects();
                break;
              case "courses":
                $this->handleAdminCourses();
                break;
            }
          }
          break;
        }
    }
  }

  public function handleAdminAdmins()
  {
    $manager = new AdminManager();
    $this->current_table["admins"] = $manager->getAllAdmins();
    $_SESSION["current_table"] = $this->current_table;
    if (isset($_POST["operation"])) {
      $manager = new AdminManager();

      if ($_POST["operation"] == "save_changes") {
        $modified_table = $_POST["modified_table"] ?? [];
        $manager->updateChanges($modified_table);
      } else if (str_contains($_POST["operation"], "delete")) {
        $array = explode("|", $_POST["operation"]);
        $manager->delete($array[1]);
      } else if ($_POST["operation"] == "add") {
        $admin = new Admin();
        $admin->name = htmlspecialchars($_POST["new_admin"]["name"] ?? "");
        $admin->surname = htmlspecialchars($_POST["new_admin"]["surname"] ?? "");
        $admin->email = htmlspecialchars($_POST["new_admin"]["email"] ?? "");
        $admin->phone_number = htmlspecialchars($_POST["new_admin"]["phone_number"] ?? "");
        if ($admin->validate())
          $manager->add($admin);
      }

      $this->current_table["admins"] = $manager->getAllAdmins();
      $_SESSION["current_table"] = $this->current_table;
    }
  }

  public function handleAdminTeachers()
  {
    $manager = new TeacherManager();
    $this->current_table["teachers"] = $manager->getAllTeachers();
    $this->current_table["subject_teachers"] = $manager->getAllTeacherSubjects();
    $manager = new SubjectManager();
    $this->current_table["subjects"] = $manager->getAllSubjects();
    $_SESSION["current_table"] = $this->current_table;
    if (isset($_POST["operation"])) {
      $manager = new TeacherManager();

      if ($_POST["operation"] == "save_changes") {
        $modified_table = $_POST["modified_table"] ?? [];
        $manager->updateChanges($modified_table);
      } else if (str_contains($_POST["operation"], "delete")) {
        $array = explode("|", $_POST["operation"]);
        $manager->delete($array[1]);
      } else if ($_POST["operation"] == "add") {
        $teacher = new Teacher();
        $teacher->name = htmlspecialchars($_POST["new_teacher"]["name"] ?? "");
        $teacher->surname = htmlspecialchars($_POST["new_teacher"]["surname"] ?? "");
        $teacher->email = htmlspecialchars($_POST["new_teacher"]["email"] ?? "");
        $teacher->phone_number = htmlspecialchars($_POST["new_teacher"]["phone_number"] ?? "");
        $teacher->teaching_subjects = htmlspecialchars($_POST["new_teacher"]["teaching_subjects"] ?? "");
        if ($teacher->validate())
          $manager->add($teacher);
      }

      $this->current_table["teachers"] = $manager->getAllTeachers();
      $this->current_table["subject_teachers"] = $manager->getAllTeacherSubjects();
      $_SESSION["current_table"] = $this->current_table;
    }
  }

  public function handleAdminStudents()
  {
    $manager = new StudentManager();
    $this->current_table["students"] = $manager->getAllStudents();
    $_SESSION["current_table"] = $this->current_table;
    if (isset($_POST["operation"])) {
      $manager = new StudentManager();

      if ($_POST["operation"] == "save_changes") {
        $modified_table = $_POST["modified_table"] ?? [];
        $manager->updateChanges($modified_table);
      } else if (str_contains($_POST["operation"], "delete")) {
        $array = explode("|", $_POST["operation"]);
        $manager->delete($array[1]);
      } else if ($_POST["operation"] == "add") {
        $student = new Student();
        $student->name = htmlspecialchars($_POST["new_student"]["name"] ?? "");
        $student->surname = htmlspecialchars($_POST["new_student"]["surname"] ?? "");
        $student->email = htmlspecialchars($_POST["new_student"]["email"] ?? "");
        $student->phone_number = htmlspecialchars($_POST["new_student"]["phone_number"] ?? "");
        if (isset($_POST["new_student"]["tuition_enabled"]))
          $student->tuition_enabled = ($_POST["new_student"]["tuition_enabled"] == "t");
        else
          $student->tuition_enabled = null;
        if ($student->validate())
          $manager->add($student);
      }

      $this->current_table["students"] = $manager->getAllStudents();
      $_SESSION["current_table"] = $this->current_table;
    }
  }

  public function handleAdminSubjects()
  {
    $manager = new SubjectManager();
    $this->current_table["subjects"] = $manager->getAllSubjects();
    $_SESSION["current_table"] = $this->current_table;
    if (isset($_POST["operation"])) {
      $manager = new SubjectManager();

      if ($_POST["operation"] == "save_changes") {
        $modified_table = $_POST["modified_table"] ?? [];
        $manager->updateChanges($modified_table);
      } else if (str_contains($_POST["operation"], "delete")) {
        $array = explode("|", $_POST["operation"]);
        $manager->delete($array[1]);
      } else if ($_POST["operation"] == "add") {
        $subject = new Subject();
        $subject->subject = htmlspecialchars($_POST["new_subject"]["subject"] ?? "");
        if ($subject->validate())
          $manager->add($subject);
      }

      $this->current_table["subjects"] = $manager->getAllSubjects();
      $_SESSION["current_table"] = $this->current_table;
    }
  }

  public function handleAdminCourses()
  {
    $manager = new CourseManager();
    $this->current_table["courses"] = $manager->getAllCourses();

    $this->current_table["course_teachers"] = $manager->getAllCourseTeachers();
    $this->current_table["course_students"] = $manager->getAllCourseStudents();
    $manager = new StudentManager();
    $this->current_table["students"] = $manager->getAllStudents();

    $manager = new TeacherManager();

    foreach ($this->current_table["courses"] as $course) {
      $this->temp["selected_subject"][$course["id"]] = $manager->getAllSubjectTeachers($course["subject_id"]);
      $_SESSION["temp"] = $this->temp;
    }
    $this->temp["selected_subject_insert"] = $_POST["new_course"]["subject"] ?? "";
    if (isset($this->temp["selected_subject_insert"]) && !empty($this->temp["selected_subject_insert"])) {
      $this->current_table["teachers"] = $manager->getAllSubjectTeachers($this->temp["selected_subject_insert"]);
      $_SESSION["temp"] = $this->temp;
    }
    $this->current_table["teacher_subjects"] = $manager->getAllTeacherSubjects();
    $manager = new SubjectManager();
    $this->current_table["subjects"] = $manager->getAllSubjects();
    $_SESSION["current_table"] = $this->current_table;

    if (isset($_POST["operation"])) {
      unset($_SESSION["temp"]["selected_subject_insert"]);
      $manager = new CourseManager();

      if ($_POST["operation"] == "save_changes") {
        $modified_table = $_POST["modified_table"] ?? [];
        $manager->updateChanges($modified_table);
      } else if (str_contains($_POST["operation"], "delete")) {
        $array = explode("|", $_POST["operation"]);
        $manager->delete($array[1]);
      } else if ($_POST["operation"] == "add") {
        $course = new Course();
        $course->name = htmlspecialchars($_POST["new_course"]["name"] ?? "");
        $course->description = htmlspecialchars($_POST["new_course"]["description"] ?? "");
        $course->status = htmlspecialchars($_POST["new_course"]["status"] ?? "");
        $course->subject = htmlspecialchars($_POST["new_course"]["subject"] ?? "");
        $course->teachers = $_POST["new_course"]["teachers"] ?? "";
        $course->students = $_POST["new_course"]["students"] ?? "";

        if ($course->validate())
          $manager->add($course);
      }

      $manager = new CourseManager();
      $this->current_table["courses"] = $manager->getAllCourses();
      $this->current_table["course_teachers"] = $manager->getAllCourseTeachers();
      $this->current_table["course_students"] = $manager->getAllCourseStudents();

      $manager = new TeacherManager();
      foreach ($this->current_table["courses"] as $course) {
        $this->temp["selected_subject"][$course["id"]] = $manager->getAllSubjectTeachers($course["id"]);
        $_SESSION["temp"] = $this->temp;
      }

      foreach ($this->current_table["courses"] as $course) {
        $this->temp["selected_subject"][$course["id"]] = $manager->getAllSubjectTeachers($course["subject_id"]);
        $_SESSION["temp"] = $this->temp;
      }
      $_SESSION["current_table"] = $this->current_table;
    }
  }

  public function handleTeacherClient()
  {
    switch ($this->page) {
      case "Teacher/Login.php": {
          $manager = new TeacherManager();
          $teacher = new Teacher();
          $teacher->name = htmlspecialchars($_POST["name"] ?? "");
          $teacher->surname = htmlspecialchars($_POST["surname"] ?? "");
          if ($manager->validate($teacher)) {
            $this->user = serialize($teacher);
            $_SESSION["user"] = $this->user;
            $this->page = "Teacher/Edit.php";
            $_SESSION["page"] = $this->page;
          }
          break;
        }
      case "Teacher/Edit.php": {
          // LOG OUT CASE
          if (isset($_POST["logout"])) {
            session_unset();
            break;
          }

          // PRESSED SELECT ON TEACHER EDITING CHOICES
          $this->edit_selection = $_POST["edit_selection"] ?? null;
          $_SESSION["edit_selection"] = $this->edit_selection;

          // HANDLING ALL EDITING/VIEWING LOGIC
          if (isset($this->edit_selection)) {
            switch ($this->edit_selection) {
              case "myaccount":
                $this->handleTeacherAccount();
                break;
              case "courses":
                $this->handleTeacherCourses();
                break;
            }
          }
          break;
        }
    }
  }

  public function handleTeacherAccount()
  {
    $manager = new TeacherManager();
    $this->current_table["teachers"] = $manager->getTeacher($this->user->name, $this->user->surname);
    LogManager::info(var_export($this->current_table["teachers"], true));
    $manager = new SubjectManager();
    $this->current_table["subjects"] = $manager->getAllSubjects();

    $_SESSION["current_table"] = $this->current_table;

    if (isset($_POST["operation"])) {
      $manager = new TeacherManager();

      if ($_POST["operation"] == "save_changes") {
        $modified_table = $_POST["modified_table"] ?? [];
        $manager->updateChanges($modified_table);
      }

      $this->current_table["teachers"] = $manager->getTeacher($this->user->name, $this->user->surname);
      $_SESSION["current_table"] = $this->current_table;
    }
  }

  public function handleTeacherCourses()
  {
    $manager = new TeacherManager();
    $this->current_table["teachers"] = $manager->getTeacher($this->user->name, $this->user->surname);

    $manager = new CourseManager();
    $this->current_table["course_teachers"] = $manager->getCoursesOfTeacher($this->current_table["teachers"]["id"]);

    $_SESSION["current_table"] = $this->current_table;

    if (isset($_POST["operation"])) {
      $manager = new CourseManager();

      if ($_POST["operation"] == "save_changes") {
        $modified_table = $_POST["modified_table"] ?? [];
        $manager->updateDescription($modified_table);
      }
    }

    $manager = new TeacherManager();
    $this->current_table["teachers"] = $manager->getTeacher($this->user->name, $this->user->surname);

    $manager = new CourseManager();
    $this->current_table["course_teachers"] = $manager->getCoursesOfTeacher($this->current_table["teachers"]["id"]);
    $_SESSION["current_table"] = $this->current_table;
  }

  public function handleStudentClient()
  {
    switch ($this->page) {
      case "Student/Login.php": {
          $manager = new StudentManager();
          $student = new Student();
          $student->name = htmlspecialchars($_POST["name"] ?? "");
          $student->surname = htmlspecialchars($_POST["surname"] ?? "");
          if ($manager->validate($student)) {
            $this->user = serialize($student);
            $_SESSION["user"] = $this->user;
            $this->page = "Student/Edit.php";
            $_SESSION["page"] = $this->page;
          }
          break;
        }
      case "Student/Edit.php": {
          // LOG OUT CASE
          if (isset($_POST["logout"])) {
            session_unset();
            break;
          }

          // PRESSED SELECT ON TEACHER EDITING CHOICES
          $this->edit_selection = $_POST["edit_selection"] ?? null;
          $_SESSION["edit_selection"] = $this->edit_selection;

          // HANDLING ALL EDITING/VIEWING LOGIC
          if (isset($this->edit_selection)) {
            switch ($this->edit_selection) {
              case "myaccount":
                $this->handleStudentAccount();
                break;
              case "courses":
                $this->handleStudentCourses();
                break;
            }
          }
          break;
        }
    }
  }

  public function handleStudentAccount()
  {
    $manager = new StudentManager();
    $this->current_table["students"] = $manager->getStudent($this->user->name, $this->user->surname);

    $_SESSION["current_table"] = $this->current_table;

    if (isset($_POST["operation"])) {
      $manager = new StudentManager();

      if ($_POST["operation"] == "save_changes") {
        $modified_table = $_POST["modified_table"] ?? [];
        $manager->updateChanges($modified_table);
      }

      $this->current_table["teachers"] = $manager->getStudent($this->user->name, $this->user->surname);
      $_SESSION["current_table"] = $this->current_table;
    }
  }

  public function handleStudentCourses()
  {
    $manager = new StudentManager();
    $this->current_table["students"] = $manager->getStudent($this->user->name, $this->user->surname);

    $manager = new CourseManager();
    $this->current_table["courses"] = $manager->getAllCourses();
    $this->current_table["course_students"] = $manager->getCoursesOfStudent($this->current_table["students"]["id"]);
    LogManager::info(var_export($this->current_table["course_students"], true));

    $_SESSION["current_table"] = $this->current_table;

    if (isset($_POST["operation"])) {
      $manager = new CourseManager();

      if ($_POST["operation"] == "save_changes") {
        $modified_table = $_POST["modified_table"] ?? [];
        $manager->updateUser($modified_table, $this->current_table["students"]["id"]);
      }
    }

    $manager = new StudentManager();
    $this->current_table["students"] = $manager->getStudent($this->user->name, $this->user->surname);

    $manager = new CourseManager();
    $this->current_table["course_students"] = $manager->getCoursesOfStudent($this->current_table["students"]["id"]);

    $_SESSION["current_table"] = $this->current_table;
  }
}
