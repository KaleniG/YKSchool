<?php

namespace App\Controllers;

use App\Config\LogManager;
use App\Config\Path;
use App\Models\Admin;
use App\Models\AdminManager;
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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      switch ($this->page) {
        case "Admin/Login.php": {
            $manager = new AdminManager();
            $admin = new Admin();
            $admin->name = $_POST["name"] ?? "";
            $admin->surname = $_POST["surname"] ?? "";
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
            if (isset($_POST["submit_edit_selection"])) {
              $this->edit_selection = $_POST["edit_selection"] ?? null;
              $_SESSION["edit_selection"] = $this->edit_selection;
            }

            // HANDLING ALL EDITING LOGIC
            if (isset($this->edit_selection)) {
              switch ($this->edit_selection) {

                // EDITING LOGIC FOR ADMIN
                case "admins": {
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
                        $admin->name = $_POST["new_admin"]["name"] ?? "";
                        $admin->surname = $_POST["new_admin"]["surname"] ?? "";
                        $admin->email = $_POST["new_admin"]["email"] ?? "";
                        $admin->phone_number = $_POST["new_admin"]["phone_number"] ?? "";
                        if ($admin->validate())
                          $manager->add($admin);
                      }

                      $this->current_table["admins"] = $manager->getAllAdmins();
                      $_SESSION["current_table"] = $this->current_table;
                    }
                    break;
                  }

                  // TEACHER EDITING LOGIC
                case "teachers": {
                    $manager = new TeacherManager();
                    $this->current_table["teachers"] = $manager->getAllTeachers();
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
                        $teacher->name = $_POST["new_teacher"]["name"] ?? "";
                        $teacher->surname = $_POST["new_teacher"]["surname"] ?? "";
                        $teacher->email = $_POST["new_teacher"]["email"] ?? "";
                        $teacher->phone_number = $_POST["new_teacher"]["phone_number"] ?? "";
                        if ($teacher->validate())
                          $manager->add($teacher);
                      }

                      $this->current_table["teachers"] = $manager->getAllTeachers();
                      $_SESSION["current_table"] = $this->current_table;
                    }
                    break;
                  }

                  // STUDENT EDITING LOGIC
                case "students": {
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
                        $student->name = $_POST["new_student"]["name"] ?? "";
                        $student->surname = $_POST["new_student"]["surname"] ?? "";
                        $student->email = $_POST["new_student"]["email"] ?? "";
                        $student->phone_number = $_POST["new_student"]["phone_number"] ?? "";
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
                    break;
                  }

                  // SUBJECT EDITING LOGIC
                case "subjects": {
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
                        $subject->subject = $_POST["new_subject"]["subject"] ?? "";
                        if ($subject->validate())
                          $manager->add($subject);
                      }

                      $this->current_table["subjects"] = $manager->getAllSubjects();
                      $_SESSION["current_table"] = $this->current_table;
                    }
                    break;
                  }
              }
            }



            break;
          }
      }

      header("Location: index.php");
      exit;
    }

    include Path::views($this->page);
  }
}
