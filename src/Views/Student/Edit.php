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
  <title>Student</title>
</head>

<body>
  <form method="post" id="main_form">
    <button type="submit" name="page" value="Home.php">Logout</button>
    <select name="edit_selection" onchange='selected_subject_submit();'>
      <option>Choose an option</option>
      <option value="myaccount" <?= ($this->edit_selection == "myaccount") ? "selected" : ""; ?>>My Account</option>
      <option value="courses" <?= ($this->edit_selection == "courses") ? "selected" : ""; ?>>Courses</option>
    </select>
    <br>
    <?php

    use App\Config\Path;

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