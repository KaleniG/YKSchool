<?php

namespace App\Controllers;

use App\Config\Path;

class MainController
{
  public function handleRequests()
  {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["page"])) {
      switch ($_POST["page"]) {
        case "Admin/Login.php":
          header("Location: admin.php");
          exit;
          break;
        case "Teacher/Login.php":
          header("Location: teacher.php");
          exit;
          break;
        case "Student/Login.php":
          header("Location: student.php");
          exit;
          break;
      }
    } else
      include Path::views("Home.php");
  }
}
