<tr>
  <th>Name</th>
  <th>Surname</th>
  <th>E-mail</th>
  <th>Phone Number</th>
  <th>Tuition Enabled</th>
  <th></th>
</tr>

<!-- UPDATE/DELETE -->
<?php foreach ($this->students as $student_row):
  $id = $student_row["id"];
  $name = $student_row['name'];
  $surname = $student_row['surname'];
  $email = $student_row["email"];
  $phone_number = $student_row["phone_number"];
  $checked = ($student_row["tuition_enabled"] == "t") ? "checked" : "";
?>

  <tr>
    <td><input type='text' value='<?= $name ?>' disabled></td>
    <td><input type='text' value='<?= $surname ?>' disabled></td>
    <td><input type='email' name='operation[save][<?= $id ?>][email]' value='<?= $email ?>'></td>
    <td><input type='text' name='operation[save][<?= $id ?>][phone_number]' value='<?= $phone_number ?>'></td>
    <td><input type='checkbox' name='operation[save][<?= $id ?>][tuition_enabled]' value='t' <?= $checked ?>></td>
    <td>
      <button type="submit" name="operation[delete]" value="<?= $id ?>">Delete</button>
      <script>
        (function() {
          const row = document.currentScript.parentNode.parentNode; // <tr>
          const emailInput = row.querySelector('input[name="operation[save][<?= $id ?>][email]"]');
          const phoneInput = row.querySelector('input[name="operation[save][<?= $id ?>][phone_number]"]');
          const tuitionInput = row.querySelector('input[name="operation[save][<?= $id ?>][tuition_enabled]"]');

          const saveBtn = document.createElement('button');
          saveBtn.type = 'submit';
          saveBtn.name = 'operation[save][confirm]';
          saveBtn.value = '<?= $id ?>';
          saveBtn.textContent = 'Save';

          function showSave() {
            const cell = emailInput.closest('tr').querySelector('td:last-child');
            if (!cell.contains(saveBtn)) {
              cell.appendChild(saveBtn);
            }
          }

          emailInput.addEventListener('input', showSave);
          phoneInput.addEventListener('input', showSave);
          tuitionInput.addEventListener('input', showSave);
        })();
      </script>
    </td>
  </tr>
<?php endforeach; ?>

<!-- INSERT -->
<tr>
  <td><input type='text' name='operation[add][name]'></td>
  <td><input type='text' name='operation[add][surname]'></td>
  <td><input type='email' name='operation[add][email]'></td>
  <td><input type='text' name='operation[add][phone_number]'></td>
  <td><input type='checkbox' name='operation[add][tuition_enabled]' value='t'></td>
  <td><button type='submit' name='operation[add][confirm]'>Add</button></td>
</tr>