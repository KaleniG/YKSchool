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
  <title>Administrator</title>
</head>

<body class="login">
  <form method="post" class="login">
    <h2 class="login">Administrator</h2>
    <h3 class="login">Login</h3>
    <button type="submit" name="page" value="Home.php" class="login">Home</button>
    <input type="text" name="name" placeholder="Name" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="login">
    <input type="text" name="surname" placeholder="Surname" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="login">
    <button type="submit" name="login" class="login">Login</button>
  </form>
</body>

</html>

<!-- SCRIPTS LOADING -->
<script src="assets/js/Common/Login.js"></script>