<?php

namespace App\Models;

class Teacher
{
  public $id = null;
  public $name = "";
  public $surname = "";
  public $email = "";
  public $phone_number = "";
  public $teaching_subjects = [];

  public function validate()
  {
    $valid_email = (!empty($this->email)) ? filter_var($this->email, FILTER_VALIDATE_EMAIL) : true;
    return !empty($this->name) && !empty($this->surname) && $valid_email;
  }
}
