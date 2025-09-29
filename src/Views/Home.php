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
  <title>Home</title>
</head>

<body>
  <form method="post">
    <button type="submit" name="page" value="Admin/Login.php">Enter as Administrator</button>
    <button type="submit" name="page" value="Teacher/Login.php">Enter as Teacher</button>
    <button type="submit" name="page" value="Student/Login.php">Enter as Student</button>
  </form>
</body>

</html>