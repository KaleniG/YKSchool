<?php

namespace App\Interfaces\Common\Controllers;

use App\Config\LogManager;

class MainController
{
  public function handleRequests()
  {
    include __DIR__ . '/../Views/Main.php';
  }
}
