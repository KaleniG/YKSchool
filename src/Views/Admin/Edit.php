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
  <title>Administrator</title>
</head>

<body>
  <form method="post" id="main_form" action="" class="main_form">
    <button type="submit" name="page" value="Home.php" class="nav-button">Logout</button>
    <select name="edit_selection" onchange='selected_subject_submit();' class="edit-select">
      <option>Choose an option</option>
      <option value="admins" <?= ($this->edit_selection == "admins") ? "selected" : ""; ?>>Administrators</option>
      <option value="teachers" <?= ($this->edit_selection == "teachers") ? "selected" : ""; ?>>Teachers</option>
      <option value="students" <?= ($this->edit_selection == "students") ? "selected" : ""; ?>>Students</option>
      <option value="subjects" <?= ($this->edit_selection == "subjects") ? "selected" : ""; ?>>Subjects</option>
      <option value="courses" <?= ($this->edit_selection == "courses") ? "selected" : ""; ?>>Courses</option>
    </select>
    <br>
    <?php if (!empty($this->edit_selection)): ?>
      <table class='main-table'>
      <?php endif; ?>
      <?php

      use App\Config\Path;

      switch ($this->edit_selection) {
        case "admins":
          include(Path::views("Admin/Admins.php"));
          break;
        case "teachers":
          include(Path::views("Admin/Teachers.php"));
          break;
        case "students":
          include(Path::views("Admin/Students.php"));
          break;
        case "subjects":
          include(Path::views("Admin/Subjects.php"));
          break;
        case "courses":
          include(Path::views("Admin/Courses.php"));
          break;
      }

      ?>
      <?php if (isset($this->edit_selection)): ?>
      </table>
    <?php endif; ?>
  </form>
</body>

</html>