<?php

namespace App\Controllers;

use App\Config\LogManager;
use App\Config\Path;
use App\Models\CourseManager;
use App\Models\SubjectManager;

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
    $default_selection = "home";
    $this->present_selection = $_SESSION["present_selection"] ?? $default_selection;

    $default_view_format = "table";
    $this->view_format = $_SESSION["view_format"] ?? $default_view_format;

    $this->word_filter = $_SESSION["word_filter"] ?? null;
    $this->subject_filter = $_SESSION["subject_filter"] ?? null;
  }

  public function handleRequests()
  {
    $this->loadSession();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      switch ($this->page) {
        case "Home.php":
          $this->handleHome();
          break;
        case "Guest/Present.php":
          $this->handlePresent();
          break;
      }

      $self = $_SERVER['PHP_SELF'];
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
    // PRESENT OPTION LOADING
    if (isset($_SESSION["present_selection"]))
      $this->present_selection = $_SESSION["present_selection"];
    else {
      $this->present_selection = $_POST["present_selection"] ?? null;
      $_SESSION["present_selection"] = $this->present_selection;
    }
    if (isset($_POST["present_selection"]) && $_POST["present_selection"] != $_SESSION["present_selection"]) {
      $this->present_selection = $_POST["present_selection"];
      $_SESSION["present_selection"] = $this->present_selection;
    }

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
      $len = strlen(htmlspecialchars($_POST["search"]["word_filter"]));
      if ($len >= 3 || $len == 0) {
        $this->word_filter = htmlspecialchars($_POST["search"]["word_filter"]) ?? null;
        $_SESSION["word_filter"] = $this->word_filter;
      }
    }
    if (isset($_POST["search"]["word_filter"]) && $_POST["search"]["word_filter"] != $_SESSION["word_filter"]) {
      $len = strlen(htmlspecialchars($_POST["search"]["word_filter"]));
      if ($len >= 3 || $len == 0) {
        $this->word_filter = htmlspecialchars($_POST["search"]["word_filter"]);
        $_SESSION["word_filter"] = $this->word_filter;
      }
    }

    // SUBJECT FILTER LOADING
    if (isset($_SESSION["subject_filter"]))
      $this->subject_filter = $_SESSION["subject_filter"];
    else {
      $this->subject_filter = $_POST["search"]["subject_filter"] ?? null;
      $_SESSION["subject_filter"] = $this->subject_filter;
    }
    if (isset($_POST["search"]["subject_filter"]) && $_POST["search"]["subject_filter"] != $_SESSION["subject_filter"]) {
      $this->subject_filter = $_POST["search"]["subject_filter"];
      $_SESSION["subject_filter"] = $this->subject_filter;
    }

    // HANDLING ALL PRESENT OPTIONS
    if (isset($this->present_selection)) {
      switch ($this->present_selection) {
        case "home":
          $this->handlePresentHome();
          break;
        case "advancedresearch":
          $this->handlePresentAdvancedResearch();
          break;
      }
    }
  }

  private function handlePresentHome()
  {
    // SESSION/DATABASE COURSES RELOADING WITH ALL COURSES
    $manager = new CourseManager();
    $this->courses = $manager->getAllCoursesWithFaceValue() ?? [];
    $_SESSION["courses"] = $this->courses;
  }

  private function handlePresentAdvancedResearch()
  {
    // SESSION/DATABASE COURSES RELOADING WITH FILTERED COURSES
    $manager = new CourseManager();
    $this->courses = $manager->getAllCoursesWithFilter($this->word_filter, $this->subject_filter) ?? [];
    $_SESSION["courses"] = $this->courses;
  }
}
