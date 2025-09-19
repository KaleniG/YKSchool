<?php

namespace App\Interfaces\Common\Controllers;

use App\Config\LogManager;
use App\Config\Model;
use App\Config\Path;
use App\Interfaces\Administration\Models\AdministratorValidator;

class MainController
{
  private $page = null;
  private $user = null;

  private function handleRendering()
  {
    switch ($this->page) {
      case "home": {
          include(Path::common("Views/Home.php"));
          break;
        }
      case "administrator": {
          $this->handleAdministratorRender();
          break;
        }
    }
  }

  public function handleRequests()
  {
    $defaultPage = "home";

    if (isset($_POST['page']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
      $_SESSION['page'] = $_POST['page'];
    }

    $this->page = $_SESSION['page'] ?? $defaultPage;
    $this->user = $_SESSION['user'] ?? null;

    if (isset($this->user) && isset($_POST["logout"])) {
      unset($this->user, $_SESSION['user']);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      switch ($this->page) {
        case "administrator": {
            $this->handleAdministratorForm();
            break;
          }
      }

      header("Location: index.php");
      exit;
    }

    include(Path::common("Views/Layouts/Main.php"));
  }

  private function handleAdministratorForm()
  {
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $validator = new AdministratorValidator();
    if ($validator->validateAdministrator($name, $surname)) {
      unset($this->user);
      $this->user["administrator"] = [$name, $surname];
      $_SESSION["user"] = $this->user;
    }
  }

  private function handleAdministratorRender()
  {
    if (isset($this->user["administrator"])) {
      include(Path::administrator("Views/Home.php"));
    } else {
      include(Path::administrator("Views/Login.php"));
    }
  }
}
