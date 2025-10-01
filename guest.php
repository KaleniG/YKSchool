<?php

require __DIR__ . "/vendor/autoload.php";

use App\Controllers\GuestController;

session_start();

$controller = new GuestController;
$controller->handleRequests();
