<?php

namespace App\Controllers;

use App\Config\Log;
use App\Config\Path;
use App\Models\Course;
use App\Models\CourseManager;
use App\Models\Student;
use App\Models\StudentManager;
use App\Models\Subject;
use App\Models\SubjectManager;
use App\Models\Teacher;
use App\Models\TeacherManager;

/*
$_POST["page"] $_SESSION["page"] -> contain the path to the page to load relative to the Views folder
$_SESSION["user"] -> on login stores the user data (name, surname...)
$_SESSION["edit_selection"] -> on login stores the value from the select input to decide what content to display
$_SESSION["subjects"] -> on edit subjects option stores all subjects
$_SESSION["courses"] -> on edit courses option stores all courses
*/

class TeacherController
{
  private $page = null;
  private $user = [];
  private $subjects = [];
  private $courses = [];

  private $edit_selection = null;

  private function loadSession()
  {
    // PAGE HANDLING
    $defaultPage = "Teacher/Login.php";
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["page"])) $_SESSION["page"] = $_POST["page"];
    $this->page = $_SESSION["page"] ?? $defaultPage;

    // TABLES HANDLING
    $this->user = $_SESSION["user"] ?? [];
    $this->subjects = $_SESSION["subjects"] ?? [];
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
        case "Teacher/Login.php":
          $this->handleLogin();
          break;
        case "Teacher/Edit.php":
          $this->handleEdit();
          break;
      }

      header("Location: teacher.php");
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
    $teacher = new Teacher();
    $teacher->name = htmlspecialchars($_POST["name"] ?? "");
    $teacher->surname = htmlspecialchars($_POST["surname"] ?? "");

    $manager = new TeacherManager();
    if ($teacher = $manager->validate($teacher)) {
      $this->user = [
        "id" => $teacher->id,
        "name" => $teacher->name,
        "surname" => $teacher->surname,
        "email" => $teacher->email,
        "phone_number" => $teacher->phone_number,
        "teaching_subjects" => $teacher->teaching_subjects
      ];
      $_SESSION["user"] = $this->user;

      $this->page = "Teacher/Edit.php";
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
    // SESSION/DATABASE SUBJECTS LOADING (BUT ONLY ONCE)
    if (isset($_SESSION["subjects"]))
      $this->subjects = $_SESSION["subjects"];
    else {
      $manager = new SubjectManager();
      $this->subjects = $manager->getAllSubjects() ?? [];
      $_SESSION["subjects"] = $this->subjects;
    }

    // OPERATIONS ON PERSONAL TEACHER ACCOUNT
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
        $manager->update($changes);
      }

      // SESSION VALUES REALOAD ON CHANGE
      $this->user = $manager->getTeacher($this->user["id"]) ?? [];
      $_SESSION["user"] = $this->user;
    }
  }

  private function handleEditCourses()
  {
    // SESSION/DATABASE COURSES LOADING (BUT ONLY ONCE)
    if (isset($_SESSION["courses"]))
      $this->courses = $_SESSION["courses"];
    else {
      $manager = new TeacherManager();
      $this->courses = $manager->getTeacherCourses($this->user["id"]) ?? [];
      $_SESSION["courses"] = $this->courses;
    }

    // OPERATIONS ON PERSONAL TEACHER ACCOUNT
    if (isset($_POST["operation"])) {
      $manager = new CourseManager();

      if (isset($_POST["operation"]["save"]["confirm"])) {
        $id = $_POST["operation"]["save"]["confirm"];
        $changes = [
          "id" => $id,
          "description" => htmlspecialchars($_POST["operation"]["save"][$id]["description"] ?? "")
        ];
        $manager->updateDescription($changes);
      }

      // SESSION VALUES REALOAD ON CHANGE
      $manager = new CourseManager();
      $this->courses = $manager->getAllCourses() ?? [];
      $_SESSION["courses"] = $this->courses;
    }
  }
}
