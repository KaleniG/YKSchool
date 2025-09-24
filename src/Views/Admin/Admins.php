<tr>
  <th>Name</th>
  <th>Surname</th>
  <th>E-mail</th>
  <th>Phone Number</th>
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

  echo ("<tr>
    <td><input type='text' value='{$name}' autocorrect='off' autocapitalize='off' spellcheck='false' disabled></td>
    <td><input type='text' value='{$surname}' autocorrect='off' autocapitalize='off' spellcheck='false' disabled></td>
    <td><input type='email' name='modified_table[{$id}][email]' value='{$email}' autocapitalize='off' spellcheck='false'></td>
    <td><input type='text' name='modified_table[{$id}][phone_number]' value='{$phone_number}' autocorrect='off' autocapitalize='off' spellcheck='false'></td>");
  if ($name == $this->user->name && $surname == $this->user->surname)
    echo ("<td><button type='submit' class='nav-button' disabled>Delete</button></td>");
  else
    echo ("<td><button type='submit' name='operation' value='delete|{$id}' class='nav-button'>Delete</button></td>");
  echo ("</tr>");
}
?>
<tr>
  <td><input type='text' name='new_admin[name]' autocorrect='off' autocapitalize='on' spellcheck='false'></td>
  <td><input type='text' name='new_admin[surname]' autocorrect='off' autocapitalize='on' spellcheck='false'></td>
  <td><input type='email' name='new_admin[email]' autocapitalize='off' spellcheck='false'></td>
  <td><input type='text' name='new_admin[phone_number]' autocorrect='off' autocapitalize='off' spellcheck='false'></td>
  <td><button type='submit' name='operation' value='add' class='nav-button'>Add</button></td>
</tr>