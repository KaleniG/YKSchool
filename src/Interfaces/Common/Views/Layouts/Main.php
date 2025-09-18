<?php

use App\Config\AssetManager;
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
    <form><button type="submit" name="page" value="home">Home</button>

      <?php
      if ($this->page != 'home') {
        include(Path::common("Views/Home.php"));
      }

      ?>

</body>

</html>