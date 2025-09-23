<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administrator</title>
  <script>
    function selected_subject_submit() {
      document.getElementById('main_form').submit();
    }
  </script>
</head>

<body>
  <form method="post" id="main_form" action="">
    <button type="submit" name="logout">Logout</button>
    <br>
    <select name="edit_selection" onchange='selected_subject_submit();'>
      <option>Choose an option</option>
      <option value="admins" <?= ($this->edit_selection == "admins") ? "selected" : ""; ?>>Administrators</option>
      <option value="teachers" <?= ($this->edit_selection == "teachers") ? "selected" : ""; ?>>Teachers</option>
      <option value="students" <?= ($this->edit_selection == "students") ? "selected" : ""; ?>>Students</option>
      <option value="subjects" <?= ($this->edit_selection == "subjects") ? "selected" : ""; ?>>Subjects</option>
      <option value="courses" <?= ($this->edit_selection == "courses") ? "selected" : ""; ?>>Courses</option>
    </select>
    <br>
    <?php


    use App\Config\Path;

    if (!empty($this->edit_selection))
      echo ("<table border='2'>");

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

    if (isset($this->edit_selection))
      echo ("</table><br><button type='submit' name='operation' value='save_changes'>Save Changes</button>");
    ?>
  </form>
</body>

</html>