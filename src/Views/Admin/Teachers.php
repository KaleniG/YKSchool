<tr>
  <th>Name</th>
  <th>Surname</th>
  <th>E-mail</th>
  <th>Phone Number</th>
  <th>Teaching Subjects</th>
  <th style="min-width: 180px;"></th>
</tr>

<!-- UPDATE/DELETE -->
<?php foreach ($this->teachers as $teacher_row):
  $id = $teacher_row["id"];
  $name = $teacher_row['name'];
  $surname = $teacher_row['surname'];
  $email = $teacher_row["email"];
  $phone_number = $teacher_row["phone_number"];
  $teaching_subjects = $teacher_row["teaching_subjects"];
?>
  <tr>
    <td><input type='text' value='<?= $name ?>' disabled></td>
    <td><input type='text' value='<?= $surname ?>' disabled></td>
    <td><input type='email' name='operation[save][<?= $id ?>][email]' value='<?= $email ?>'></td>
    <td><input type='text' name='operation[save][<?= $id ?>][phone_number]' value='<?= $phone_number ?>'></td>
    <td><select name='operation[save][<?= $id ?>][teaching_subjects][]' size='4' multiple>

        <?php foreach ($this->subjects as $subject_row):
          $subject_id = $subject_row['id'];
          $subject_name = $subject_row['name'];
          $selected = in_array($subject_id, $teaching_subjects) ? "selected" : "";
        ?>
          <option value='<?= $subject_id ?>' <?= $selected ?>><?= $subject_name ?></option>
        <?php endforeach; ?>
      </select>
    </td>
    <td>
      <button type="submit" name="operation[delete]" value="<?= $id ?>" class="nav-button">Delete</button>
      <script>
        (function() {
          const row = document.currentScript.parentNode.parentNode; // <tr>
          const emailInput = row.querySelector('input[name="operation[save][<?= $id ?>][email]"]');
          const phoneInput = row.querySelector('input[name="operation[save][<?= $id ?>][phone_number]"]');
          const teachingSubjectsInput = row.querySelector('select[name="operation[save][<?= $id ?>][teaching_subjects][]"]');

          const saveBtn = document.createElement('button');
          saveBtn.type = 'submit';
          saveBtn.name = 'operation[save][confirm]';
          saveBtn.value = '<?= $id ?>';
          saveBtn.className = 'nav-button';
          saveBtn.textContent = 'Save';

          function showSave() {
            const cell = teachingSubjectsInput.closest('tr').querySelector('td:last-child');
            if (!cell.contains(saveBtn)) {
              cell.appendChild(saveBtn);
            }
          }

          emailInput.addEventListener('input', showSave);
          phoneInput.addEventListener('input', showSave);
          teachingSubjectsInput.addEventListener('change', showSave);
        })();
      </script>
    </td>
  </tr>
<?php endforeach; ?>

<!-- ADD -->
<tr>
  <td><input type='text' name='operation[add][name]'></td>
  <td><input type='text' name='operation[add][surname]'></td>
  <td><input type='email' name='operation[add][email]'></td>
  <td><input type='text' name='operation[add][phone_number]'></td>
  <td><select name='operation[add][teaching_subjects][]' size="4" multiple>

      <?php foreach ($this->subjects as $subject_row):
        $subject_id = $subject_row['id'];
        $subject_name = $subject_row['name'];
      ?>
        <option value='<?= $subject_id ?>'><?= $subject_name ?></option>
      <?php endforeach; ?>
    </select></td>
  <td><button type='submit' name='operation[add][confirm]' class='nav-button'>Add</button></td>
</tr>