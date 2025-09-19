<?php

namespace App\Interfaces\Administration\Models;

use App\Config\LogManager;
use App\Interfaces\Common\Models\UserValidator;

class AdministratorValidator extends UserValidator
{
  public function validateAdministrator($name, $surname)
  {
    return $this->validate("administrators", $name, $surname);
  }
}
