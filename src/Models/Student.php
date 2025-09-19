<?php

namespace App\Models;

class Student
{
  public $name = "";
  public $surname = "";
  public $email = "";
  public $phone_number = "";
  public $tuition_enabled = null;

  public function validate()
  {
    return !empty($this->name) && !empty($this->surname) && isset($this->tuition_enabled);
  }
}
