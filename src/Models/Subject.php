<?php

namespace App\Models;

class Subject
{
  public $subject = "";

  public function validate()
  {
    return !empty($this->subject);
  }
}
