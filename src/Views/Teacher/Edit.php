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

<body class="edit">
  <form method="post" id="main_form" class="edit">
    <div class="edit-navbar">
      <button type="submit" name="page" value="Home.php" class="edit">Logout</button>
      <select name="edit_selection" onchange='selected_subject_submit();' class="edit-navbar">
        <option>Select an option</option>
        <option value="myaccount" <?= ($this->edit_selection == "myaccount") ? "selected" : ""; ?>>My Account</option>
        <option value="courses" <?= ($this->edit_selection == "courses") ? "selected" : ""; ?>>Courses</option>
      </select>
    </div>
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