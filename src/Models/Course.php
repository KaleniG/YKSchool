<?php

namespace App\Models;

class Course
{
  public $name = "";
  public $description = "";
  public $status = "";
  public $subject = "";
  public $teachers = [];
  public $students = [];

  public function validate()
  {
    return !empty($this->name) && !empty($this->status) && !empty($this->subject);
  }
}
