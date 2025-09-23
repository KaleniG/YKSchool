<tr>
  <th>Name</th>
  <th>Surname</th>
  <th>E-mail</th>
  <th>Phone Number</th>
  <th>Tuition Enabled</th>
  <th></th>
</tr>

<?php
// UPDATE/DELETE
foreach ($this->current_table[$this->edit_selection] as $row) {
  $id = $row["id"];
  $name = $row['name'];
  $surname = $row['surname'];
  $email = $row["email"];
  $phone_number = $row["phone_number"];
  $checked = ($row["tuition_enabled"] == "t") ? "checked" : "";
  echo ("<tr>
    <td>{$name}</td>
    <td>{$surname}</td>
    <td><input type='email' name='modified_table[{$id}][email]' value='{$email}'></td>
    <td><input type='text' name='modified_table[{$id}][phone_number]' value='{$phone_number}'></td>
    <td><input type='checkbox' name='modified_table[{$id}][tuition_enabled]' value='t' $checked></td>
    <td><button type='submit' name='operation' value='delete|{$id}'>Delete</button></td>
    </tr>");
}
?>

<tr>
  <td><input type='text' name='new_student[name]'></td>
  <td><input type='text' name='new_student[surname]'></td>
  <td><input type='email' name='new_student[email]'></td>
  <td><input type='text' name='new_student[phone_number]'></td>
  <td><input type='checkbox' name='new_student[tuition_enabled]' value='t'></td>
  <td><button type='submit' name='operation' value='add'>Add</button></td>
</tr>