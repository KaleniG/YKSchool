<tr>
  <th>Subject</th>
  <th></th>
</tr>

<?php
// UPDATE/DELETE
foreach ($this->current_table[$this->edit_selection] as $row) {
  $id = $row["id"];
  $subject = $row["subject"];
  echo ("<tr>
    <td><input type='text' name='modified_table[{$id}][subject]' value='{$subject}'></td>
    <td><button type='submit' name='operation' value='delete|{$id}'>Delete</button></td>
    </tr>");
}
?>

<tr>
  <td><input type='text' name='new_subject[subject]'></td>
  <td><button type='submit' name='operation' value='add'>Add</button></td>
</tr>