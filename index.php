<?php
if (session_status() === PHP_SESSION_NONE)
  session_start();

require_once __DIR__ . '/vendor/autoload.php';

use App\Interfaces\Common\Controllers\MainController;

$theController = new MainController();
$theController->handleRequests();
