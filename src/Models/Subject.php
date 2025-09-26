<?php

namespace App\Models;

class Subject
{
  public $id = null;
  public $name = "";

  public function validate()
  {
    return !empty($this->name);
  }
}
