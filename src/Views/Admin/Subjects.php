<tr>
  <th>Subject</th>
  <th style="min-width: 180px;"></th>
</tr>


<!-- UPDATE/DELETE -->
<?php foreach ($this->subjects as $subject_row):
  $id = $subject_row["id"];
  $name = $subject_row['name'];
?>
  <tr>
    <td><input type='text' name='operation[save][<?= $id ?>][name]' value='<?= $name ?>'></td>
    <td>
      <button type="submit" name="operation[delete]" value="<?= $id ?>" class="nav-button">Delete</button>
      <script>
        (function() {
          const row = document.currentScript.parentNode.parentNode; // <tr>
          const nameInput = row.querySelector('input[name="operation[save][<?= $id ?>][name]"]');

          const saveBtn = document.createElement('button');
          saveBtn.type = 'submit';
          saveBtn.name = 'operation[save][confirm]';
          saveBtn.value = '<?= $id ?>';
          saveBtn.className = 'nav-button';
          saveBtn.textContent = 'Save';

          function showSave() {
            const cell = nameInput.closest('tr').querySelector('td:last-child');
            if (!cell.contains(saveBtn)) {
              cell.appendChild(saveBtn);
            }
          }

          nameInput.addEventListener('input', showSave);
        })();
      </script>
    </td>
  </tr>
<?php endforeach; ?>

<!-- INSERT -->
<tr>
  <td><input type='text' name='operation[add][name]'></td>
  <td><button type='submit' name='operation[add][confirm]' class='nav-button'>Add</button></td>
</tr>