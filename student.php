<?php

require __DIR__ . "/vendor/autoload.php";

use App\Controllers\StudentController;

session_start();

$controller = new StudentController;
$controller->handleRequests();
