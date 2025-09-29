<?php

namespace App\Controllers;

use App\Config\LogManager;
use App\Config\Path;
use App\Models\Course;
use App\Models\CourseManager;
use App\Models\Student;
use App\Models\StudentManager;
use App\Models\Subject;
use App\Models\SubjectManager;

class StudentController
{
  private $page = null;
  private $user = [];
  private $courses = [];

  private $edit_selection = null;

  private function loadSession()
  {
    // PAGE HANDLING
    $defaultPage = "Student/Login.php";
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["page"])) $_SESSION["page"] = $_POST["page"];
    $this->page = $_SESSION["page"] ?? $defaultPage;

    // TABLES HANDLING
    $this->user = $_SESSION["user"] ?? [];
    $this->courses = $_SESSION["courses"] ?? [];

    // NAVIGATION/TEMPORARIES HANDLING
    $this->edit_selection = $_SESSION["edit_selection"] ?? null;
  }

  public function handleRequests()
  {
    $this->loadSession();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      switch ($this->page) {
        case "Home.php":
          $this->handleHome();
          break;
        case "Student/Login.php":
          $this->handleLogin();
          break;
        case "Student/Edit.php":
          $this->handleEdit();
          break;
      }

      header("Location: student.php");
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
    $student = new Student();
    $student->name = htmlspecialchars($_POST["name"] ?? "");
    $student->surname = htmlspecialchars($_POST["surname"] ?? "");

    $manager = new StudentManager();
    if ($student = $manager->validate($student)) {
      $this->user = [
        "id" => $student->id,
        "name" => $student->name,
        "surname" => $student->surname,
        "email" => $student->email,
        "phone_number" => $student->phone_number,
        "tuition_enabled" => $student->tuition_enabled
      ];
      $_SESSION["user"] = $this->user;

      $this->page = "Student/Edit.php";
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
        case "Home.php":
          $this->handleHome();
          break;
        case "myaccount":
          $this->handleEditMyAccount();
          break;
        case "courses":
          $this->handleEditCourses();
          break;
      }
    }
  }

  private function handleEditMyAccount()
  {
    // OPERATIONS ON PERSONAL TEACHER ACCOUNT
    if (isset($_POST["operation"])) {
      $manager = new StudentManager();

      if (isset($_POST["operation"]["save"]["confirm"])) {
        $id = $_POST["operation"]["save"]["confirm"];
        $changes = [
          "id" => $id,
          "email" => htmlspecialchars($_POST["operation"]["save"][$id]["email"]),
          "phone_number" => htmlspecialchars($_POST["operation"]["save"][$id]["phone_number"])
        ];
        $manager->update($changes);
      }

      // SESSION VALUES REALOAD ON CHANGE
      $this->user = $manager->getStudent($this->user["id"]) ?? [];
      $_SESSION["user"] = $this->user;
    }
  }

  private function handleEditCourses()
  {
    // SESSION/DATABASE COURSES LOADING (BUT ONLY ONCE)
    if (isset($this->courses)) {
      $manager = new CourseManager();
      $this->courses = $manager->getAllCoursesOnStudent($this->user["id"]) ?? [];
      $_SESSION["courses"] = $this->courses;
    }

    // OPERATIONS ON PERSONAL TEACHER ACCOUNT
    if (isset($_POST["operation"])) {
      $manager = new CourseManager();

      if (isset($_POST["operation"]["save"]["confirm"])) {
        $id = $_POST["operation"]["save"]["confirm"];
        $changes = [
          "student_id" => $this->user["id"],
          "course_id" => $id,
          "is_student_subscribed" => ($_POST["operation"]["save"][$id]["is_student_subscribed"] == "t") ? true : false
        ];
        $manager->updateCourseSubscription($changes);
      }

      // SESSION VALUES REALOAD ON CHANGE
      $manager = new CourseManager();
      $this->courses = $manager->getAllCoursesOnStudent($this->user["id"]) ?? [];
      $_SESSION["courses"] = $this->courses;
    }
  }
}
