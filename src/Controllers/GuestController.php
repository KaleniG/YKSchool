<?php

namespace App\Controllers;

use App\Config\Path;
use App\Models\CourseManager;
use App\Models\SubjectManager;

/*
$_POST["page"] $_SESSION["page"] -> contain the path to the page to load relative to the Views folder
$_POST["present_selection"] $_SESSION["present_selection"] -> stores the value of the page to load
$_POST["view_format"] $_SESSION["view_format"] -> stores the value of the page to load for the way to visualize the courses
$_POST["word_filter"] $_SESSION["word_filter"] -> stores the user input from the textbox
$_POST["subject_filter"] $_SESSION["subject_filter"] -> stores the user input from the select box with all of the subjects
$_POST["view_format"] $_SESSION["view_format"] -> stores the value of the page to load for the way to visualize the courses
$_SESSION["subjects"] -> stores all of the subjects from the start
$_SESSION["courses"] -> stores all of the courses from the start, on press of the Home button or the use of the filtered search is loaded with courses' data
*/

class GuestController
{
  private $page = null;
  private $courses = [];
  private $subjects = [];

  private $present_selection = null;
  private $word_filter = null;
  private $subject_filter = null;
  private $view_format = null;

  private function loadSession()
  {
    // PAGE HANDLING
    $defaultPage = "Guest/Present.php";
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["page"])) $_SESSION["page"] = $_POST["page"];
    $this->page = $_SESSION["page"] ?? $defaultPage;

    // TABLES HANDLING
    if (isset($_SESSION["subjects"]))
      $this->subjects = $_SESSION["subjects"];
    else {
      $manager = new SubjectManager();
      $this->subjects = $manager->getAllSubjects() ?? [];
      $_SESSION["subjects"] = $this->subjects;
    }

    if (isset($_SESSION["courses"]))
      $this->courses = $_SESSION["courses"];
    else {
      $manager = new CourseManager();
      $this->courses = $manager->getAllCoursesWithFaceValue() ?? [];
      $_SESSION["courses"] = $this->courses;
    }

    // NAVIGATION/TEMPORARIES HANDLING
    $default_view_format = "table";
    $this->view_format = $_SESSION["view_format"] ?? $default_view_format;

    $this->word_filter = $_SESSION["word_filter"] ?? null;
    $this->subject_filter = $_SESSION["subject_filter"] ?? null;
  }

  public function handleRequests()
  {
    $this->loadSession();

    if (!empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest") {
      header("Content-Type: application/json");
      echo json_encode($this->courses);
      exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      switch ($this->page) {
        case "Home.php":
          $this->handleHome();
          break;
        case "Guest/Present.php":
          $this->handlePresent();
          break;
      }

      $self = $_SERVER["PHP_SELF"];
      header("Location: {$self}");
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

  private function handlePresent()
  {
    // VIEW FORMAT LOADING
    if (isset($_SESSION["view_format"]))
      $this->view_format = $_SESSION["view_format"];
    else {
      $this->view_format = $_POST["view_format"] ?? null;
      $_SESSION["view_format"] = $this->view_format;
    }
    if (isset($_POST["view_format"]) && $_POST["view_format"] != $_SESSION["view_format"]) {
      $this->view_format = $_POST["view_format"];
      $_SESSION["view_format"] = $this->view_format;
    }

    // WORD FILTER LOADING
    if (isset($_SESSION["word_filter"]))
      $this->word_filter = $_SESSION["word_filter"];
    else {
      $rawWord = $_POST["word_filter"] ?? "";
      $len = strlen(trim($rawWord));
      if ($len >= 3 || $len === 0) {
        $this->word_filter = htmlspecialchars($rawWord);
        $_SESSION["word_filter"] = $this->word_filter;
      }
    }
    if (isset($_POST["word_filter"]) && $_POST["word_filter"] != $_SESSION["word_filter"]) {
      $rawWord = $_POST["word_filter"] ?? "";
      $len = strlen(trim($rawWord));
      if ($len >= 3 || $len === 0) {
        $this->word_filter = htmlspecialchars($rawWord);
        $_SESSION["word_filter"] = $this->word_filter;
      }
    }

    // SUBJECT FILTER LOADING
    if (isset($_SESSION["subject_filter"]))
      $this->subject_filter = $_SESSION["subject_filter"];
    else {
      $this->subject_filter = $_POST["subject_filter"] ?? null;
      $_SESSION["subject_filter"] = $this->subject_filter;
    }
    if (isset($_POST["subject_filter"]) && $_POST["subject_filter"] != $_SESSION["subject_filter"]) {
      $this->subject_filter = $_POST["subject_filter"];
      $_SESSION["subject_filter"] = $this->subject_filter;
    }
  }
}
