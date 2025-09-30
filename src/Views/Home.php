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
    <button type="submit" name="page" value="admin.php" class="home">Enter as Administrator</button>
    <button type="submit" name="page" value="teacher.php" class="home">Enter as Teacher</button>
    <button type="submit" name="page" value="student.php" class="home">Enter as Student</button>
    <button type="submit" name="page" value="guest.php" class="home">Enter as Guest</button>
  </form>
</body>

</html>