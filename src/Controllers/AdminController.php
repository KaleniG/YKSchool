<?php

namespace App\Controllers;

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

/*
$_POST["name"] $_POST["surname] -> credentials on login
$_POST["page"] $_SESSION["page"] -> contain the path to the page to load relative to the Views folder
$_SESSION["user"] -> on login stores the user (admin) data (name, surname...)
$_POST["edit_selection"] $_SESSION["edit_selection"] -> on login stores the value from the select input to decide what content to display
$_SESSION["admins"] -> on edit administrators option stores all admins
$_SESSION["teachers"] -> on edit teachers option stores all teachers
$_SESSION["students"] -> on edit students option stores all students
$_SESSION["subjects"] -> on edit subjects option stores all subjects
$_SESSION["courses"] -> on edit courses option stores all courses
$_SESSION["new_course_subject_selection"] -> on edit courses option stores the selected subject when trying to add a new course

$_POST["operation"] -> array containing an operation assciative array:
  ["save"]
    ["confirm"] -> returns the id of the element to save
    ["id"] -> used in format ["{$id}"] to access the filtered element's parameters that changed
      ["...parameter name..."] -> parameter value to save for an element
  ["delete"] -> returns the id of the element to delete
  ["add"]
    ["confirm"] -> just a submit value needed to actually add new element, does not contain any value
    ["...parameter name..."] -> parameter value to add to a new element

$_POST["operation"]["add"]["subject"] -> special exception, used to load $_SESSION["new_course_subject_selection"]
*/

class AdminController
{
  private $page = null;
  private $user = [];
  private $admins = [];
  private $teachers = [];
  private $students = [];
  private $subjects = [];
  private $courses = [];

  private $edit_selection = null;
  private $new_course_subject_selection = null;

  private function loadSession()
  {
    // PAGE HANDLING
    $defaultPage = "Admin/Login.php";
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["page"])) $_SESSION["page"] = $_POST["page"];
    $this->page = $_SESSION["page"] ?? $defaultPage;

    // TABLES HANDLING
    $this->user = $_SESSION["user"] ?? [];
    $this->admins = $_SESSION["admins"] ?? [];
    $this->teachers = $_SESSION["teachers"] ?? [];
    $this->students = $_SESSION["students"] ?? [];
    $this->subjects = $_SESSION["subjects"] ?? [];
    $this->courses = $_SESSION["courses"] ?? [];

    // NAVIGATION/TEMPORARIES HANDLING
    $this->edit_selection = $_SESSION["edit_selection"] ?? null;
    $this->new_course_subject_selection = $_SESSION["new_course_subject_selection"] ?? null;
  }

  public function handleRequests()
  {
    $this->loadSession();

    if (!empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest") {
      header("Content-Type: application/json");
      echo json_encode($this->teachers);
      exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      switch ($this->page) {
        case "Home.php":
          $this->handleHome();
          break;
        case "Admin/Login.php":
          $this->handleLogin();
          break;
        case "Admin/Edit.php":
          $this->handleEdit();
          break;
      }

      header("Location: admin.php");
      exit;
    }

    include Path::views($this->page);
  }

  private function handleHome()
  {
    session_unset();
    header("Location: index.php");
    exit;
  }

  private function handleLogin()
  {
    // USER DATABASE VALIDATION
    $admin = new Admin();
    $admin->name = htmlspecialchars($_POST["name"] ?? "");
    $admin->surname = htmlspecialchars($_POST["surname"] ?? "");

    $manager = new AdminManager();
    if ($admin = $manager->validate($admin)) {
      $this->user = [
        "id" => $admin->id,
        "name" => $admin->name,
        "surname" => $admin->surname,
        "email" => $admin->email,
        "phone_number" => $admin->phone_number
      ];
      $_SESSION["user"] = $this->user;

      $this->page = "Admin/Edit.php";
      $_SESSION["page"] = $this->page;

      $manager->prepareAll();
    }
  }

  private function handleEdit()
  {
    // EDITING OPTION LOADING
    if (isset($_SESSION["edit_selection"]))
      $this->edit_selection = $_SESSION["edit_selection"];
    else {
      $this->edit_selection = $_POST["edit_selection"] ?? null;
      $_SESSION["edit_selection"] = $this->edit_selection;
    }
    if (isset($_POST["edit_selection"]) && $_POST["edit_selection"] != $_SESSION["edit_selection"]) {
      $this->edit_selection = $_POST["edit_selection"];
      $_SESSION["edit_selection"] = $this->edit_selection;
    }

    // HANDLING ALL EDITING OPTIONS
    if (isset($this->edit_selection)) {
      switch ($this->edit_selection) {
        case "admins":
          $this->handleEditAdmins();
          break;
        case "teachers":
          $this->handleEditTeachers();
          break;
        case "students":
          $this->handleEditStudents();
          break;
        case "subjects":
          $this->handleEditSubjects();
          break;
        case "courses":
          $this->handleEditCourses();
          break;
      }
    }
  }

  private function handleEditAdmins()
  {
    // UNSETTING TEMPORARY SESSION VARIABLES
    unset($this->new_course_subject_selection);
    unset($_SESSION["new_course_subject_selection"]);

    // SESSION/DATABASE ADMINS LOADING (BUT ONLY ONCE)
    $manager = new AdminManager();
    if (isset($_SESSION["admins"]))
      $this->admins = $_SESSION["admins"];
    else {
      $this->admins = $manager->getAllAdmins() ?? [];
      $_SESSION["admins"] = $this->admins;
    }

    // OPERATIONS ON ADMINS
    if (isset($_POST["operation"])) {
      if (isset($_POST["operation"]["save"]["confirm"])) {
        $id = $_POST["operation"]["save"]["confirm"];
        $changes = [
          "id" => $id,
          "email" => htmlspecialchars($_POST["operation"]["save"][$id]["email"]),
          "phone_number" => htmlspecialchars($_POST["operation"]["save"][$id]["phone_number"])
        ];
        if (filter_var($changes["email"], FILTER_VALIDATE_EMAIL))
          $manager->update($changes);
      } else if (isset($_POST["operation"]["delete"])) {
        $manager->delete($_POST["operation"]["delete"]);
      } else if (isset($_POST["operation"]["add"]["confirm"])) {
        $admin = new Admin();
        $admin->name = htmlspecialchars($_POST["operation"]["add"]["name"] ?? "");
        $admin->surname = htmlspecialchars($_POST["operation"]["add"]["surname"] ?? "");
        $admin->email = htmlspecialchars($_POST["operation"]["add"]["email"] ?? "");
        $admin->phone_number = htmlspecialchars($_POST["operation"]["add"]["phone_number"] ?? "");
        if ($admin->validate())
          $manager->add($admin);
      }

      // SESSION VALUES REALOAD ON CHANGE
      $this->admins = $manager->getAllAdmins() ?? [];
      $_SESSION["admins"] = $this->admins;
    }
  }

  private function handleEditTeachers()
  {
    // UNSETTING TEMPORARY SESSION VARIABLES
    unset($this->new_course_subject_selection);
    unset($_SESSION["new_course_subject_selection"]);

    // SESSION/DATABASE TEACHERS LOADING
    if (isset($_SESSION["teachers"]))
      $this->teachers = $_SESSION["teachers"];
    else {
      $manager = new TeacherManager();
      $this->teachers = $manager->getAllTeachersWithSubjects() ?? [];
      $_SESSION["teachers"] = $this->teachers;
    }

    // SESSION/DATABASE SUBJECTS LOADING (BUT ONLY ONCE)
    if (isset($_SESSION["subjects"]))
      $this->subjects = $_SESSION["subjects"];
    else {
      $manager = new SubjectManager();
      $this->subjects = $manager->getAllSubjects() ?? [];
      $_SESSION["subjects"] = $this->subjects;
    }

    // OPERATIONS ON TEACHERS
    if (isset($_POST["operation"])) {
      $manager = new TeacherManager();

      if (isset($_POST["operation"]["save"]["confirm"])) {
        $id = $_POST["operation"]["save"]["confirm"];
        $changes = [
          "id" => $id,
          "email" => htmlspecialchars($_POST["operation"]["save"][$id]["email"]),
          "phone_number" => htmlspecialchars($_POST["operation"]["save"][$id]["phone_number"]),
          "teaching_subjects" => $_POST["operation"]["save"][$id]["teaching_subjects"] ?? []
        ];
        if (filter_var($changes["email"], FILTER_VALIDATE_EMAIL))
          $manager->update($changes);
        $manager->update($changes);
      } else if (isset($_POST["operation"]["delete"])) {
        $manager->delete($_POST["operation"]["delete"]);
      } else if (isset($_POST["operation"]["add"]["confirm"])) {
        $teacher = new Teacher();
        $teacher->name = htmlspecialchars($_POST["operation"]["add"]["name"] ?? "");
        $teacher->surname = htmlspecialchars($_POST["operation"]["add"]["surname"] ?? "");
        $teacher->email = htmlspecialchars($_POST["operation"]["add"]["email"] ?? "");
        $teacher->phone_number = htmlspecialchars($_POST["operation"]["add"]["phone_number"] ?? "");
        $teacher->teaching_subjects = $_POST["operation"]["add"]["teaching_subjects"] ?? [];
        if ($teacher->validate())
          $manager->add($teacher);
      }

      // SESSION VALUES REALOAD ON CHANGE
      $manager = new TeacherManager();
      $this->teachers = $manager->getAllTeachersWithSubjects() ?? [];
      $_SESSION["teachers"] = $this->teachers;
    }
  }

  private function handleEditStudents()
  {
    // UNSETTING TEMPORARY SESSION VARIABLES
    unset($this->new_course_subject_selection);
    unset($_SESSION["new_course_subject_selection"]);

    // SESSION/DATABASE STUDENTS LOADING (BUT ONLY ONCE)
    $manager = new StudentManager();
    if (isset($_SESSION["students"]))
      $this->students = $_SESSION["students"];
    else {
      $this->students = $manager->getAllStudents() ?? [];
      $_SESSION["students"] = $this->students;
    }

    // OPERATIONS ON STUDENTS
    if (isset($_POST["operation"])) {
      if (isset($_POST["operation"]["save"]["confirm"])) {
        $id = $_POST["operation"]["save"]["confirm"];
        $changes = [
          "id" => $id,
          "email" => htmlspecialchars($_POST["operation"]["save"][$id]["email"]),
          "phone_number" => htmlspecialchars($_POST["operation"]["save"][$id]["phone_number"]),
          "tuition_enabled" => (htmlspecialchars($_POST["operation"]["save"][$id]["tuition_enabled"] ?? "f") == "t") ? true : false
        ];
        if (filter_var($changes["email"], FILTER_VALIDATE_EMAIL))
          $manager->update($changes);
        $manager->update($changes);
      } else if (isset($_POST["operation"]["delete"])) {
        $manager->delete($_POST["operation"]["delete"]);
      } else if (isset($_POST["operation"]["add"]["confirm"])) {
        $student = new Student();
        $student->name = htmlspecialchars($_POST["operation"]["add"]["name"] ?? "");
        $student->surname = htmlspecialchars($_POST["operation"]["add"]["surname"] ?? "");
        $student->email = htmlspecialchars($_POST["operation"]["add"]["email"] ?? "");
        $student->phone_number = htmlspecialchars($_POST["operation"]["add"]["phone_number"] ?? "");
        $student->tuition_enabled = (htmlspecialchars($_POST["operation"]["add"]["tuition_enabled"] ?? "f") == "t") ? true : false;
        if ($student->validate())
          $manager->add($student);
      }

      // SESSION VALUES REALOAD ON CHANGE
      $this->students = $manager->getAllStudents() ?? [];
      $_SESSION["students"] = $this->students;
    }
  }

  private function handleEditSubjects()
  {
    // UNSETTING TEMPORARY SESSION VARIABLES
    unset($this->new_course_subject_selection);
    unset($_SESSION["new_course_subject_selection"]);

    // SESSION/DATABASE SUBJECTS LOADING (BUT ONLY ONCE)
    $manager = new SubjectManager();
    if (isset($_SESSION["subjects"]))
      $this->subjects = $_SESSION["subjects"];
    else {
      $this->subjects = $manager->getAllSubjects() ?? [];
      $_SESSION["subjects"] = $this->subjects;
    }

    // OPERATIONS ON SUBJECTS
    if (isset($_POST["operation"])) {
      if (isset($_POST["operation"]["save"]["confirm"])) {
        $id = $_POST["operation"]["save"]["confirm"];
        $changes = [
          "id" => $id,
          "name" => htmlspecialchars($_POST["operation"]["save"][$id]["name"])
        ];
        $manager->update($changes);
      } else if (isset($_POST["operation"]["delete"])) {
        $manager->delete($_POST["operation"]["delete"]);
      } else if (isset($_POST["operation"]["add"]["confirm"])) {
        $subject = new Subject();
        $subject->name = htmlspecialchars($_POST["operation"]["add"]["name"] ?? "");
        if ($subject->validate())
          $manager->add($subject);
      }

      // SESSION VALUES REALOAD ON CHANGE
      $this->subjects = $manager->getAllSubjects() ?? [];
      $_SESSION["subjects"] = $this->subjects;
    }
  }

  private function handleEditCourses()
  {
    // SESSION/DATABASE COURSES LOADING
    $manager = new CourseManager();
    if (isset($_SESSION["courses"]))
      $this->courses = $_SESSION["courses"];
    else {
      $this->courses = $manager->getAllCoursesWithDetails() ?? [];
      $_SESSION["courses"] = $this->courses;
    }

    // SESSION/DATABASE SUBJECTS LOADING (BUT ONLY ONCE)
    if (isset($_SESSION["subjects"]))
      $this->subjects = $_SESSION["subjects"];
    else {
      $manager = new SubjectManager();
      $this->subjects = $manager->getAllSubjects() ?? [];
      $_SESSION["subjects"] = $this->subjects;
    }

    // SESSION/DATABASE TEACHERS LOADING (BUT ONLY ONCE)
    if (isset($_SESSION["teachers"]))
      $this->teachers = $_SESSION["teachers"];
    else {
      $manager = new TeacherManager();
      $this->teachers = $manager->getAllTeachersWithSubjects() ?? [];
      $_SESSION["teachers"] = $this->teachers;
    }

    // SESSION/DATABASE STUDENTS LOADING (BUT ONLY ONCE)
    if (isset($_SESSION["students"]))
      $this->students = $_SESSION["students"];
    else {
      $manager = new StudentManager();
      $this->students = $manager->getAllStudents() ?? [];
      $_SESSION["students"] = $this->students;
    }

    // SESSION CURRENTLY SELECTED INSERT SUBJECT LOADING
    if (isset($_SESSION["new_course_subject_selection"]))
      $this->new_course_subject_selection = $_SESSION["new_course_subject_selection"];
    else {
      $this->new_course_subject_selection = $_POST["operation"]["add"]["subject"] ?? null;
      $_SESSION["new_course_subject_selection"] = $this->new_course_subject_selection;
    }
    if (isset($_POST["operation"]["add"]["subject"]) && $_POST["operation"]["add"]["subject"] != $_SESSION["new_course_subject_selection"]) {
      $this->new_course_subject_selection = $_POST["operation"]["add"]["subject"];
      $_SESSION["new_course_subject_selection"] = $this->new_course_subject_selection;
    }

    // OPERATIONS ON COURSES
    if (isset($_POST["operation"])) {
      $manager = new CourseManager();

      if (isset($_POST["operation"]["save"]["confirm"])) {
        $id = $_POST["operation"]["save"]["confirm"];
        $changes = [
          "id" => $id,
          "name" => htmlspecialchars($_POST["operation"]["save"][$id]["name"]),
          "description" => htmlspecialchars($_POST["operation"]["save"][$id]["description"]),
          "status" => htmlspecialchars($_POST["operation"]["save"][$id]["status"]),
          "subject" => htmlspecialchars($_POST["operation"]["save"][$id]["subject"]),
          "course_students" => $_POST["operation"]["save"][$id]["course_students"] ?? [],
          "course_teachers" => $_POST["operation"]["save"][$id]["course_teachers"] ?? []
        ];
        $manager->update($changes);

        // UNSETTING THE VARIABLE SINCE WE ARE NOT TRYING TO ADD ANYMORE
        unset($this->new_course_subject_selection);
        unset($_SESSION["new_course_subject_selection"]);
      } else if (isset($_POST["operation"]["delete"])) {
        $manager->delete($_POST["operation"]["delete"]);

        // UNSETTING THE VARIABLE SINCE WE ARE NOT TRYING TO ADD ANYMORE
        unset($this->new_course_subject_selection);
        unset($_SESSION["new_course_subject_selection"]);
      } else if (isset($_POST["operation"]["add"]["confirm"])) {
        $course = new Course();
        $course->name = htmlspecialchars($_POST["operation"]["add"]["name"] ?? "");
        $course->description = htmlspecialchars($_POST["operation"]["add"]["description"] ?? "");
        $course->status = htmlspecialchars($_POST["operation"]["add"]["status"] ?? "");
        $course->subject = htmlspecialchars($_POST["operation"]["add"]["subject"] ?? "");
        $course->students = $_POST["operation"]["add"]["students"] ?? [];
        $course->teachers = $_POST["operation"]["add"]["teachers"] ?? [];
        if ($course->validate())
          $manager->add($course);

        // SESSION VALUES REALOAD ON CHANGE (BUT ONLY FOR THE SUBJECT SELECTION IF IT CHANGED)
        if (isset($_POST["operation"]["add"]["subject"]) && $_POST["operation"]["add"]["subject"] != $_SESSION["new_course_subject_selection"]) {
          $this->new_course_subject_selection = $_POST["operation"]["add"]["subject"];
          $_SESSION["new_course_subject_selection"] = $this->new_course_subject_selection;
        }
      }

      // SESSION VALUES REALOAD ON CHANGE
      $this->courses = $manager->getAllCoursesWithDetails() ?? [];
      $_SESSION["courses"] = $this->courses;
    }
  }
}
