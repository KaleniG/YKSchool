<?php

namespace App\Config;

class AssetManager
{
  private $css = [
    'assets/css/style.css'
  ];

  private $sql = [
    'assets/sql/ykschool.sql'
    //'assets/sql/add_admin.sql'
  ];

  private $js = [
    'assets/js/submit.js'
  ];

  public function importCSS()
  {
    $id = session_id();
    $allfiles = "";
    foreach ($this->css as $css_file)
      $allfiles .= "<link rel='stylesheet' href='{$css_file}?v={$id}3s'>";
    return $allfiles;
  }

  public function importSQL()
  {
    $sql_array = [];

    foreach ($this->sql as $file) {
      $sql_str = file_get_contents($file);
      if ($sql_str === false) {
        LogManager::error("Failed to read SQL file");
        return;
      }
      array_push($sql_array, $sql_str);
    }

    return $sql_array;
  }

  public function importJS()
  {
    $allfiles = "";
    foreach ($this->js as $js_file)
      $allfiles .= "<script src='{$js_file}'></script>";
    return $allfiles;
  }
}
