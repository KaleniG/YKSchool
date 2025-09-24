<?php

use App\Config\AssetManager;

$asset = new AssetManager();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?= $asset->importCSS(); ?>
  <title>Teacher</title>
</head>

<body>
  <h1>TEACHER</h1>
  <form method="post">
    <button type="submit" name="page" value="Home.php">Home</button>
    <br>
    <input type="text" name="name">
    <br>
    <input type="text" name="surname">
    <br>
    <button type="submit" name="login">Login</button>
  </form>
</body>

</html>