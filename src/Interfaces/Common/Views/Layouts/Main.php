<?php

use App\Config\AssetManager;
use App\Config\LogManager;
use App\Config\Path;

$assets = new AssetManager();
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>YK School</title>
  <?= $assets->importCSS() ?>
</head>

<body>
  <header>
    <form method="post">
      <button type="submit" name="page" value="home">Home</button>
      <?php
      if (isset($this->user))
        echo ("<button type='submit' name='logout' value='yes'>Logout</button>");
      ?>

      <?php
      $this->handleRendering();
      ?>
</body>

</html>