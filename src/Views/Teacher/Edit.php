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
  <?= $asset->importJS(); ?>
  <title>Teacher</title>
</head>

<body>
  <form method="post" id="main_form" action="" class="main_form">
    <button type="submit" name="logout" class="nav-button">Logout</button>
    <select name="edit_selection" onchange='selected_subject_submit();' class="edit-select">
      <option>Choose an option</option>
      <option value="myaccount" <?= ($this->edit_selection == "myaccount") ? "selected" : ""; ?>>My Account</option>
      <option value="courses" <?= ($this->edit_selection == "courses") ? "selected" : ""; ?>>Courses</option>
    </select>
    <br>
    <?php

    use App\Config\Path;

    switch ($this->edit_selection) {
      case "myaccount":
        include(Path::views("Teacher/MyAccount.php"));
        break;
      case "courses":
        include(Path::views("Teacher/Courses.php"));
        break;
    }
    ?>
  </form>
</body>

</html>