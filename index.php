<?php

require __DIR__ . "/vendor/autoload.php";

use App\Controllers\MainController;

session_start();

$controller = new MainController;
$controller->handleRequests();
