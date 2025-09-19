<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <form method="post">
    <button type="submit" name="logout">Logout</button>
    <br>
    <select name="edit_selection">
      <option>Choose an option</option>
      <option value="admins" <?= ($this->edit_selection == "admins") ? "selected" : ""; ?>>Administrators</option>
      <option value="teachers" <?= ($this->edit_selection == "teachers") ? "selected" : ""; ?>>Teachers</option>
      <option value="students" <?= ($this->edit_selection == "students") ? "selected" : ""; ?>>Students</option>
      <option value="subjects" <?= ($this->edit_selection == "subjects") ? "selected" : ""; ?>>Subjects</option>
      <option value="courses" <?= ($this->edit_selection == "courses") ? "selected" : ""; ?>>Courses</option>
    </select>
    <button type="submit" name="submit_edit_selection">Select</button>
    <br>
    <?php







    switch ($this->edit_selection) {
      case "teachers":
      case "admins": {
          echo ("
            <table border='2'>
              <tr>
                <th>Name</th>
                <th>Surname</th>
                <th>E-mail</th>
                <th>Phone Number</th>
                <th></th>
              </tr>
            ");
          break;
        }
      case "students": {
          echo ("
            <table border='2'>
              <tr>
                <th>Name</th>
                <th>Surname</th>
                <th>E-mail</th>
                <th>Phone Number</th>
                <th>Tuition Enabled</th>
                <th></th>
              </tr>
            ");
          break;
        }
      case "subjects": {
          echo ("
            <table border='2'>
              <tr>
                <th>Subject</th>
                <th></th>
              </tr>
            ");
          break;
        }
    }

    

    foreach ($this->current_table[$this->edit_selection] as $row) {

      switch ($this->edit_selection) {
        case "teachers":
        case "admins": {
            $id = $row["id"];
            $email = $row["email"];
            $phone_number = $row["phone_number"];
            echo ("
              <tr>
                <td>{$row['name']}</td>
                <td>{$row['surname']}</td>
                <td><input type='email' name='modified_table[{$id}][email]' value='{$email}'></td>
                <td><input type='text' name='modified_table[{$id}][phone_number]' value='{$phone_number}'></td>");
            if ($row["name"] == $this->user->name && $row["surname"] == $this->user->surname)
              echo ("<td></td>");
            else
              echo ("<td><button type='submit' name='operation' value='delete|{$id}'>Delete</button></td>");
            echo ("</tr>");
            break;
          }
        case "students": {
            $id = $row["id"];
            $email = $row["email"];
            $phone_number = $row["phone_number"];
            $tuition_enabled = ($row["tuition_enabled"] == "t") ? "checked" : "";
            echo ("
              <tr>
                <td>{$row['name']}</td>
                <td>{$row['surname']}</td>
                <td><input type='email' name='modified_table[{$id}][email]' value='{$email}'></td>
                <td><input type='text' name='modified_table[{$id}][phone_number]' value='{$phone_number}'></td>
                <td><input type='checkbox' name='modified_table[{$id}][tuition_enabled]' value='t' $tuition_enabled></td>
                <td><button type='submit' name='operation' value='delete|{$id}'>Delete</button></td>
              </tr>
            ");
            break;
          }
        case "subjects": {
            $id = $row["id"];
            $subject = $row["subject"];
            echo ("
              <tr>
                <td><input type='text' name='modified_table[{$id}][subject]' value='{$subject}'></td>
                <td><button type='submit' name='operation' value='delete|{$id}'>Delete</button></td>
              </tr>
            ");
            break;
          }
      }
    }






    switch ($this->edit_selection) {
      case "admins": {
          echo ("
            <tr>
              <td><input type='text' name='new_admin[name]'></td>
              <td><input type='text' name='new_admin[surname]'></td>
              <td><input type='email' name='new_admin[email]'></td>
              <td><input type='text' name='new_admin[phone_number]'></td>
              <td><button type='submit' name='operation' value='add'>Add</button></td>
            </tr>
          ");
          break;
        }
      case "teachers": {
          echo ("
            <tr>
              <td><input type='text' name='new_teacher[name]'></td>
              <td><input type='text' name='new_teacher[surname]'></td>
              <td><input type='email' name='new_teacher[email]'></td>
              <td><input type='text' name='new_teacher[phone_number]'></td>
              <td><button type='submit' name='operation' value='add'>Add</button></td>
            </tr>
          ");
          break;
        }
      case "students": {
          echo ("
            <tr>
              <td><input type='text' name='new_student[name]'></td>
              <td><input type='text' name='new_student[surname]'></td>
              <td><input type='email' name='new_student[email]'></td>
              <td><input type='text' name='new_student[phone_number]'></td>
              <td><input type='checkbox' name='new_student[tuition_enabled]' value='t'></td>
              <td><button type='submit' name='operation' value='add'>Add</button></td>
            </tr>
          ");
          break;
        }
      case "subjects": {
          echo ("
            <tr>
              <td><input type='text' name='new_subject[subject]'></td>
              <td><button type='submit' name='operation' value='add'>Add</button></td>
            </tr>
          ");
          break;
        }
    }

    if (isset($this->edit_selection)) {
      echo ("</table>");
      echo ("<br><button type='submit' name='operation' value='save_changes'>Save Changes</button>");
    }

    ?>
  </form>
</body>

</html>