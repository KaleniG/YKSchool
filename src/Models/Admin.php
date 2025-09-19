<?php

namespace App\Models;

class Admin
{
  public $name = "";
  public $surname = "";
  public $email = "";
  public $phone_number = "";

  public function validate()
  {
    return !empty($this->name) && !empty($this->surname);
  }
}
