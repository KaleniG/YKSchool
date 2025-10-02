<?php

use App\Config\Path;
use App\Config\AssetManager;

$asset = new AssetManager();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?= $asset->importCSS(); ?>
  <script>
    function submit() {
      document.getElementById("main_form").submit();
    }
  </script>
  <title>Student</title>
</head>

<body class="edit">
  <form method="post" id="main_form" class="edit">
    <div class="edit navbar">
      <button type="submit" name="page" value="Home.php" class="edit">Logout</button>
      <select name="edit_selection" onchange="submit();" class="edit navbar">
        <option value="" disabled selected>Select an option</option>
        <option value="myaccount" <?= ($this->edit_selection == "myaccount") ? "selected" : ""; ?>>My Account</option>
        <option value="courses" <?= ($this->edit_selection == "courses") ? "selected" : ""; ?>>Courses</option>
      </select>
    </div>
    <?php
    switch ($this->edit_selection) {
      case "myaccount":
        include(Path::views("Student/MyAccount.php"));
        break;
      case "courses":
        include(Path::views("Student/Courses.php"));
        break;
    }
    ?>
  </form>
</body>

</html>