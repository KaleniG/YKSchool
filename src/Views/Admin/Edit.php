<?php

use App\Config\Path;

$user_type = "Administrator";
$edit_options = [
  "admins" => [
    "label" => "Administrators",
    "dir" => "Admin/Admins.php"
  ],
  "teachers" => [
    "label" => "Teachers",
    "dir" => "Admin/Teachers.php"
  ],
  "students" => [
    "label" => "Students",
    "dir" => "Admin/Students.php"
  ],
  "subjects" => [
    "label" => "Subjects",
    "dir" => "Admin/Subjects.php"
  ],
  "courses" => [
    "label" => "Courses",
    "dir" => "Admin/Courses.php"
  ]
];

include Path::views("Layouts/Edit.php");
