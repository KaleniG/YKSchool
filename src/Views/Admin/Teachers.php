<tr>
  <th>Name</th>
  <th>Surname</th>
  <th>E-mail</th>
  <th>Phone Number</th>
  <th>Teaching Subjects</th>
  <th></th>
</tr>

<?php
// UPDATE/DELETE
foreach ($this->current_table[$this->edit_selection] as $row) {
  $id = $row["id"];
  $email = $row["email"];
  $name = $row['name'];
  $surname = $row['surname'];
  $phone_number = $row["phone_number"];

  echo ("<tr>
    <td>{$name}</td>
    <td>{$surname}</td>
    <td><input type='email' name='modified_table[{$id}][email]' value='{$email}'></td>
    <td><input type='text' name='modified_table[{$id}][phone_number]' value='{$phone_number}'></td>
    <td><select name='modified_table[{$id}][teaching_subjects][]' multiple>");


  // Start: Multiple subject selection logic
  $subjects_table = $this->current_table["subjects"];
  $subject_teachers_table = $this->current_table["subject_teachers"];

  $teacher_subjects_array = [];
  foreach ($subject_teachers_table as $subject_teachers_row)
    if ($subject_teachers_row["teacher_id"] == $id)
      $teacher_subjects_array = str_getcsv(trim($subject_teachers_row['subjects'], '{}'));

  foreach ($subjects_table as $subject_row) {
    $subject_name = $subject_row['subject'];
    $subject_id = $subject_row['id'];
    $selected = in_array($subject_name, $teacher_subjects_array) ? "selected" : "";
    echo ("<option value='{$subject_id}' $selected>$subject_name</option>");
  }
  // End: Multiple subject selection logic

  echo ("</select></td><td><button type='submit' name='operation' value='delete|{$id}' class='nav-button'>Delete</button></td></tr>");
}
?>
<tr>
  <td><input type='text' name='new_teacher[name]'></td>
  <td><input type='text' name='new_teacher[surname]'></td>
  <td><input type='email' name='new_teacher[email]'></td>
  <td><input type='text' name='new_teacher[phone_number]'></td>
  <td><select name='new_teacher[teaching_subjects][]' multiple>

      <?php
      // ADD
      // Start: Multiple subject selection logic
      $subjects_table = $this->current_table["subjects"];

      foreach ($subjects_table as $subject_row) {
        $subject_name = $subject_row["subject"];
        $subject_id = $subject_row["id"];
        echo ("<option value='$subject_id'>$subject_name</option>");
      }
      // End: Multiple subject selection logic
      ?>

    </select></td>
  <td><button type='submit' name='operation' value='add' class='nav-button'>Add</button></td>
</tr>