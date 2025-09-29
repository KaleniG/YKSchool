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

<body class="home">
  <form method="post" class="home">
    <button type="submit" name="page" value="Admin/Login.php" class="home">Enter as Administrator</button>
    <button type="submit" name="page" value="Teacher/Login.php" class="home">Enter as Teacher</button>
    <button type="submit" name="page" value="Student/Login.php" class="home">Enter as Student</button>
  </form>
</body>

</html>