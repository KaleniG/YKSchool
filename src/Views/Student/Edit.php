<?php

use App\Config\Path;

$user_type = "Student";
$edit_options = [
  "myaccount" => [
    "label" => "My Account",
    "dir" => "Student/MyAccount.php"
  ],

  "courses" => [
    "label" => "Courses",
    "dir" => "Student/Courses.php"
  ]
];

include Path::views("Layouts/Edit.php");
