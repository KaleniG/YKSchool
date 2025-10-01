<tr>
  <th>Name</th>
  <th>Surname</th>
  <th>E-mail</th>
  <th>Phone Number</th>
  <th>Teaching Subjects</th>
  <th></th>
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
    <td><input type='text' value='<?= $name ?>' class="edit" disabled></td>
    <td><input type='text' value='<?= $surname ?>' class="edit" disabled></td>
    <td><input type='email' name='operation[save][<?= $id ?>][email]' value='<?= $email ?>' class="edit"></td>
    <td><input type='text' name='operation[save][<?= $id ?>][phone_number]' value='<?= $phone_number ?>' class="edit"></td>
    <td><select name='operation[save][<?= $id ?>][teaching_subjects][]' size='2' class="edit" multiple>

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
      <button type="submit" name="operation[delete]" value="<?= $id ?>" class="edit option-button">Delete</button>
      <script>
        (function() {
          const row = document.currentScript.parentNode.parentNode;
          const emailInput = row.querySelector('input[name="operation[save][<?= $id ?>][email]"]');
          const phoneInput = row.querySelector('input[name="operation[save][<?= $id ?>][phone_number]"]');
          const teachingSubjectsInput = row.querySelector('select[name="operation[save][<?= $id ?>][teaching_subjects][]"]');

          const saveBtn = document.createElement('button');
          saveBtn.type = 'button';
          saveBtn.className = "edit option-button save";
          saveBtn.textContent = 'Save';

          function showSave() {
            const cell = teachingSubjectsInput.closest('tr').querySelector('td:last-child');
            if (!cell.contains(saveBtn)) {
              cell.appendChild(saveBtn);
              requestAnimationFrame(() => {
                saveBtn.classList.add('visible');
              });
            }
          }

          async function sendData() {
            const formData = new FormData();

            formData.append("operation[save][<?= $id ?>][email]", emailInput.value);
            formData.append("operation[save][<?= $id ?>][phone_number]", phoneInput.value);

            for (const option of teachingSubjectsInput.selectedOptions) {
              formData.append("operation[save][<?= $id ?>][teaching_subjects][]", option.value);
            }

            formData.append("operation[save][confirm]", "<?= $id ?>");

            try {
              const response = await fetch("admin.php", {
                method: "POST",
                body: formData
              });

            } catch (err) {
              console.error("Failed to save the course data: ", err);
            }

            if (saveBtn.isConnected) {
              requestAnimationFrame(() => {
                saveBtn.classList.remove('visible');
              });
              setTimeout(() => saveBtn.remove(), 400);
            }
          }

          emailInput.addEventListener('input', showSave);
          phoneInput.addEventListener('input', showSave);
          teachingSubjectsInput.addEventListener('change', showSave);
          saveBtn.addEventListener('click', sendData);
        })();
      </script>
    </td>
  </tr>
<?php endforeach; ?>

<!-- ADD -->
<tr>
  <td><input type='text' name='operation[add][name]' class="edit"></td>
  <td><input type='text' name='operation[add][surname]' class="edit"></td>
  <td><input type='email' name='operation[add][email]' class="edit"></td>
  <td><input type='text' name='operation[add][phone_number]' class="edit"></td>
  <td><select name='operation[add][teaching_subjects][]' size='2' class="edit" multiple>

      <?php foreach ($this->subjects as $subject_row):
        $subject_id = $subject_row['id'];
        $subject_name = $subject_row['name'];
      ?>
        <option value='<?= $subject_id ?>'><?= $subject_name ?></option>
      <?php endforeach; ?>
    </select>
  </td>
  <td><button type='submit' name='operation[add][confirm]' class="edit option-button">Add</button></td>
</tr>