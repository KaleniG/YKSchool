<?php

use App\Config\Path;

$user_type = "Teacher";
$edit_options = [
  "myaccount" => [
    "label" => "My Account",
    "dir" => "Teacher/MyAccount.php"
  ],

  "courses" => [
    "label" => "Courses",
    "dir" => "Teacher/Courses.php"
  ]
];

include Path::views("Layouts/Edit.php");
