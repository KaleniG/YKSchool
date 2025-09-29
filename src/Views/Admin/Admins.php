<tr>
  <th>Name</th>
  <th>Surname</th>
  <th>E-mail</th>
  <th>Phone Number</th>
  <th></th>
</tr>

<!-- UPDATE/DELETE -->
<?php foreach ($this->admins as $admin_row):
  $id = $admin_row["id"];
  $name = $admin_row["name"];
  $surname = $admin_row["surname"];
  $email = $admin_row["email"];
  $phone = $admin_row["phone_number"];
?>
  <tr>
    <td><input type="text" value="<?= $name ?>" disabled></td>
    <td><input type="text" value="<?= $surname ?>" disabled></td>
    <td><input type="email" name="operation[save][<?= $id ?>][email]" value="<?= $email ?>"></td>
    <td><input type="text" name="operation[save][<?= $id ?>][phone_number]" value="<?= $phone ?>"></td>
    <td>
      <?php if ($this->user["id"] != $id): ?>
        <button type="submit" name="operation[delete]" value="<?= $id ?>">Delete</button>
      <?php endif; ?>
      <script>
        (function() {
          const row = document.currentScript.parentNode.parentNode; // <tr>
          const emailInput = row.querySelector('input[name="operation[save][<?= $id ?>][email]"]');
          const phoneInput = row.querySelector('input[name="operation[save][<?= $id ?>][phone_number]"]');

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
        })();
      </script>
    </td>
  </tr>
<?php endforeach; ?>


<!-- INSERT -->
<tr>
  <td><input type='text' name='operation[add][name]' autocorrect='off' autocapitalize='on' spellcheck='false'></td>
  <td><input type='text' name='operation[add][surname]' autocorrect='off' autocapitalize='on' spellcheck='false'></td>
  <td><input type='email' name='operation[add][email]' autocapitalize='off' spellcheck='false'></td>
  <td><input type='text' name='operation[add][phone_number]' autocorrect='off' autocapitalize='off' spellcheck='false'></td>
  <td><button type='submit' name='operation[add][confirm]'>Add</button></td>
</tr>