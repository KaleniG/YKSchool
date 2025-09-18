<?php

namespace App\Interfaces\Common\Controllers;

use App\Config\LogManager;
use App\Config\Model;

class MainController
{
  private $page = null;

  public function handleHomePage() {}

  public function handleRequests()
  {
    $defaultPage = "home";

    if (isset($_POST['page']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
      $_SESSION['page'] = $_POST['page'];
    }

    $this->page = $_SESSION['page'] ?? $defaultPage;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      switch ($this->page) {
        case "home": {
            $this->handleHomePage();
            break;
          }
      }
      header("Location: index.php");
      exit();
    }

    include __DIR__ . '/../Views/Layouts/Main.php';
  }
}
