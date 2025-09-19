<?php

namespace App\Interfaces\Administration\Models;

use App\Interfaces\Common\Models\UserValidator;

class AdministratorValidator extends UserValidator
{
  public function validateAdministrator($name, $surname)
  {
    $this->validate("administrators", $name, $surname);
  }
}
