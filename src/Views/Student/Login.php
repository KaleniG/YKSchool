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
  <title>Student</title>
</head>

<body>
  <form method="post" class="login-form">
    <h2>Student Login</h2>
    <button type="submit" name="page" value="Home.php" class="nav-button">Home</button>
    <label>Name:</label>
    <input type="text" name="name" autocorrect="off" autocapitalize="off" spellcheck="false">
    <label>Surname:</label>
    <input type="text" name="surname" autocorrect="off" autocapitalize="off" spellcheck="false">
    <button type="submit" name="login" class="nav-button">Login</button>
  </form>
</body>

</html>