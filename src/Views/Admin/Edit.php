<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <form method="post" id="main_form" action="">
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
      case "teachers": {
          echo ("
            <table border='2'>
              <tr>
                <th>Name</th>
                <th>Surname</th>
                <th>E-mail</th>
                <th>Phone Number</th>
                <th>Teaching Subjects</th>
                <th></th>
              </tr>
            ");
          break;
        }
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
      case "courses": {
          echo ("
            <table border='2'>
              <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Subject</th>
                <th>Teachers</th>
                <th>Students</th>
                <th></th>
              </tr>
            ");
          break;
        }
    }



    foreach ($this->current_table[$this->edit_selection] as $row) {

      switch ($this->edit_selection) {
        case "teachers": {
            $id = $row["id"];
            $email = $row["email"];
            $phone_number = $row["phone_number"];
            $subjects_table = $this->current_table["subjects"];
            $subject_teachers_table = $this->current_table["subject_teachers"];
            echo ("
              <tr>
                <td>{$row['name']}</td>
                <td>{$row['surname']}</td>
                <td><input type='email' name='modified_table[{$id}][email]' value='{$email}'></td>
                <td><input type='text' name='modified_table[{$id}][phone_number]' value='{$phone_number}'></td>
                <td><select name='modified_table[{$id}][teaching_subjects][]' multiple>");

            $teacher_subjects_array = [];
            foreach ($subject_teachers_table as $subject_teachers_row) {
              if ($subject_teachers_row["teacher_id"] == $id) {
                $teacher_subjects_array = str_getcsv(trim($subject_teachers_row['subjects'], '{}'));
              }
            }

            foreach ($subjects_table as $subject_row) {
              $subject_name = $subject_row['subject'];
              $subject_id = $subject_row['id'];
              $selected = in_array($subject_name, $teacher_subjects_array) ? "selected" : "";
              echo ("<option value='{$subject_id}' $selected>$subject_name</option>");
            }


            echo ("</select></td>
              <td><button type='submit' name='operation' value='delete|{$id}'>Delete</button></td>
            </tr>");
            break;
          }
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
        case "courses": {
            $id = $row["id"];
            $name = $row["name"];
            $description = $row["description"];
            $status = $row["status"];

            $subjects_table = $this->current_table["subjects"];
            echo ("
              <tr>
                <td><input type='text' name='modified_table[{$id}][name]' value='{$name}'></td>
                <td><textarea name='modified_table[{$id}][description]'>$description</textarea></td>
                <td>
                  <select name='modified_table[{$id}][status]'>
                    <option>Choose an option</option>
                    <option value='Active' " . (($status == "Active") ? "selected" : "") . ">Active</option>
                    <option value='Suspended' " . (($status == "Suspended") ? "selected" : "") . ">Suspended</option>
                    <option value='UnderDevelopment' " . (($status == "UnderDevelopment") ? "selected" : "") . ">Under Development</option>
                  </select>
                </td>
                <td>
                  <select name='modified_table[{$id}][subject]'>
                    <option>Choose an option</option>
                    <option value='Active' " . (($status == "Active") ? "selected" : "") . ">Active</option>
                    <option value='Suspended' " . (($status == "Suspended") ? "selected" : "") . ">Suspended</option>
                    <option value='UnderDevelopment' " . (($status == "UnderDevelopment") ? "selected" : "") . ">Under Development</option>
                  </select>
                </td>
                <td>STUDENTS</td>
                <td>TEACHERS</td>
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
          $subjects_table = $this->current_table["subjects"];
          echo ("
            <tr>
              <td><input type='text' name='new_teacher[name]'></td>
              <td><input type='text' name='new_teacher[surname]'></td>
              <td><input type='email' name='new_teacher[email]'></td>
              <td><input type='text' name='new_teacher[phone_number]'></td>
              <td><select name='new_teacher[teaching_subjects][]' multiple>");
          foreach ($subjects_table as $subject_row) {
            $subject_name = $subject_row["subject"];
            $subject_id = $subject_row["id"];
            echo ("<option value='$subject_id'>$subject_name</option>");
          }
          echo ("</select></td>
              <td><button type='submit' name='operation' value='add'>Add</button></td>
            </tr>");
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
      case "courses": {
          $subjects_table = $this->current_table["subjects"];
          $teacher_subjects_table = $this->current_table["subject_teachers"];
          $students_table = $this->current_table["students"];
          $teachers_table = $this->current_table["teachers"];
          echo ("
              <tr>
                <td><input type='text' name='new_course[name]'></td>
                <td><textarea name='new_course[description]'></textarea></td>
                <td>
                  <select name='new_course[status]'>
                    <option>Choose an option</option>
                    <option value='Active'>Active</option>
                    <option value='Suspended'>Suspended</option>
                    <option value='UnderDevelopment'>Under Development</option>
                  </select>
                </td>
                <td><select name='new_course[subject]'>");
          foreach ($subjects_table as $subject_row) {
            $subject_name = $subject_row["subject"];
            $subject_id = $subject_row["id"];
            echo ("<option value='$subject_id'>$subject_name</option>");
          }
          echo ("</select><button type='submit' name='operation' value='select_subject'>Add</button></td>
          <td><select name='new_course[teachers][]' multiple>");

          foreach ($teachers_table as $teacher_row) {
            $teacher_id = $teacher_row["id"];
            $teacher_name = $teacher_row["name"];
            $teacher_surname = $teacher_row["surname"];
            echo ("<option value='$teacher_id'>$teacher_name $teacher_surname</option>");
          }
          echo ("</td><td><select name='new_course[students][]' multiple>");
          foreach ($students_table as $student_row) {
            $student_id = $student_row["id"];
            $student_name = $student_row["name"];
            $student_surname = $student_row["surname"];
            if ($student_row["tuition_enabled"] == "t")
              echo ("<option value='$student_id'>$student_name $student_surname</option>");
          }
          echo ("</select></td>
                <td><button type='submit' name='operation' value='add'>Add</button></td>
              </tr>");
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