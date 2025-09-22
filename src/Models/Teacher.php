<?php

namespace App\Models;

class Teacher
{
  public $name = "";
  public $surname = "";
  public $email = "";
  public $phone_number = "";
  public $teaching_subjects = [];

  public function validate()
  {
    return !empty($this->name) && !empty($this->surname);
  }
}
