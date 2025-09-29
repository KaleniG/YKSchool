<?php

require __DIR__ . "/vendor/autoload.php";

use App\Controllers\AdminController;

session_start();

$controller = new AdminController();
$controller->handleRequests();
