<tr>
  <th>Name</th>
  <th>Description</th>
  <th>Status</th>
  <th>Subject</th>
  <th>Teachers</th>
  <th>Students</th>
  <th></th>
</tr>

<?php
// UPDATE/DELETE

use App\Config\LogManager;

foreach ($this->current_table[$this->edit_selection] as $row) {
  $id = $row["id"];
  $name = $row["name"];
  $description = $row["description"];
  $status = $row["status"];
  $course_subject_id = $row["subject_id"];

  echo ("<tr>
  <td><input type='text' name='modified_table[{$id}][name]' value='{$name}'></td>
  <td><textarea name='modified_table[{$id}][description]'>$description</textarea></td>
  <td>
  <select name='modified_table[{$id}][status]'>
  <option>Choose an option</option>
  <option value='Active' " . (($status == "Active") ? "selected" : "") . ">Active</option>
  <option value='Suspended' " . (($status == "Suspended") ? "selected" : "") . ">Suspended</option>
  <option value='UnderDevelopment' " . (($status == "UnderDevelopment") ? "selected" : "") . ">Under Development</option>
  </select>
  </td><td>");

  $subjects_table = $this->current_table["subjects"];
  foreach ($subjects_table as $subject_row) {
    $subject_name = $subject_row["subject"];
    $subject_id = $subject_row["id"];
    if ($course_subject_id == $subject_id) {
      echo ("$subject_name");
      break;
    }
  }

  echo ("</td><td>");

  $course_teachers_table = $this->current_table["course_teachers"];
  if (!empty($this->temp["selected_subject"][$id])) {
    echo ("<select name='modified_table[$id][teachers][]' multiple>");

    foreach ($this->temp["selected_subject"][$id] as $teacher_row) {
      $teacher_id = $teacher_row["teacher_id"];
      $teacher_name = $teacher_row["teacher_name"];
      $teacher_surname = $teacher_row["teacher_surname"];
      $selected = "";

      foreach ($course_teachers_table as $course_teachers_row)
        if ($course_teachers_row["teacher_id"] == $teacher_id && $id == $course_teachers_row["course_id"])
          $selected = "selected";

      echo ("<option value='$teacher_id' {$selected}>$teacher_name $teacher_surname</option>");
    }

    echo ("</select>");
  }

  $course_students_table = $this->current_table["course_students"];
  $students_table = $this->current_table["students"];
  echo ("</td><td><select name='modified_table[$id][students][]' multiple>");
  foreach ($students_table as $student_row) {
    $student_id = $student_row["id"];
    $student_name = $student_row["name"];
    $student_surname = $student_row["surname"];

    $selected = "";
    foreach ($course_students_table as $course_students_row)
      if ($course_students_row["student_id"] == $student_id && $id == $course_students_row["course_id"])
        $selected = "selected";

    if ($student_row["tuition_enabled"] == "t")
      echo ("<option value='$student_id' {$selected}>$student_name $student_surname</option>");
  }
  echo ("</select></td><td><button type='submit' name='operation' value='delete|$id'>Delete</button></td></tr>");
}

?>
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
  <td>
    <select name='new_course[subject]' onchange='selected_subject_submit();'>

      <?php
      // ADD
      $subjects_table = $this->current_table["subjects"];
      echo ("<option value=''>Choose a subject</option>");
      foreach ($subjects_table as $subject_row) {
        $subject_name = $subject_row["subject"];
        $subject_id = $subject_row["id"];
        $selected = ($this->temp["selected_subject_insert"] == $subject_id) ? "selected" : "";

        echo ("<option value='$subject_id' $selected>$subject_name</option>");
      }
      echo ("</select></td><td>");

      $teachers_table = $this->current_table["teachers"];
      LogManager::info(var_export($teachers_table, true));

      if (!empty($teachers_table)) {
        echo ("<select name='new_course[teachers][]' multiple>");

        foreach ($teachers_table as $teacher_row) {
          $teacher_id = $teacher_row["teacher_id"];
          $teacher_name = $teacher_row["teacher_name"];
          $teacher_surname = $teacher_row["teacher_surname"];
          echo ("<option value='$teacher_id'>$teacher_name $teacher_surname</option>");
        }

        echo ("</select>");
      }

      $students_table = $this->current_table["students"];
      if (!empty($students_table)) {
        echo ("</td><td><select name='new_course[students][]' multiple>");
        foreach ($students_table as $student_row) {
          $student_id = $student_row["id"];
          $student_name = $student_row["name"];
          $student_surname = $student_row["surname"];
          if ($student_row["tuition_enabled"] == "t")
            echo ("<option value='$student_id'>$student_name $student_surname</option>");
        }
        echo ("</select></td>");
      } else
        echo ("<td></td>");
      echo ("<td><button type='submit' name='operation' value='add'>Add</button></td></tr>");
