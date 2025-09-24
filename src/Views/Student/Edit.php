<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student</title>
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