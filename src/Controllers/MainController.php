<?php

namespace App\Controllers;

use App\Config\Path;

/*
$_POST["page"] -> contain the name of the file to load relative to index.php
*/

class MainController
{
  public function handleRequests()
  {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["page"])) {
      $page = $_POST["page"];
      unset($_POST);
      header("Location: {$page}");
      exit;
    }

    include Path::views("Common/Home.php");
  }
}
