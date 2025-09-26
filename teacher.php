<?php

require __DIR__ . "/vendor/autoload.php";

use App\Controllers\TeacherController;

session_start();

$controller = new TeacherController;
$controller->handleRequests();
