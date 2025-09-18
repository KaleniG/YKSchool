<?php

namespace App\Config;

class AssetManager
{
  private $css = [
    'assets/css/style.css'
  ];

  public function importCSS()
  {
    $allfiles = "";
    foreach ($this->css as $css_file) {
      $allfiles .= "<link rel='stylesheet' href='{$css_file}'>";
    }

    return $allfiles;
  }
}
