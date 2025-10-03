<table class="edit">
  <tr>
    <th>Name</th>
    <th>Surname</th>
    <th>E-mail</th>
    <th>Phone Number</th>
    <th>Teaching Subjects</th>
    <th>Operations</th>
  </tr>

  <!-- UPDATE/DELETE -->
  <?php foreach ($this->teachers as $teacher_row):
    $id = $teacher_row["id"];
    $name = $teacher_row["name"];
    $surname = $teacher_row["surname"];
    $email = $teacher_row["email"];
    $phone_number = $teacher_row["phone_number"];
    $teaching_subjects = $teacher_row["teaching_subjects"];
  ?>
    <tr data-id="<?= $id ?>">
      <td><input type="text" value="<?= $name ?>" class="edit" disabled></td>
      <td><input type="text" value="<?= $surname ?>" class="edit" disabled></td>
      <td><input type="email" name="operation[save][<?= $id ?>][email]" value="<?= $email ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="edit"></td>
      <td><input type="number" name="operation[save][<?= $id ?>][phone_number]" value="<?= $phone_number ?>" autocomplete="off" autocorrect="off" class="edit"></td>
      <td>
        <select name="operation[save][<?= $id ?>][teaching_subjects][]" size="2" class="edit" multiple>
          <?php foreach ($this->subjects as $subject_row):
            $subject_id = $subject_row["id"];
            $subject_name = $subject_row["name"];
            $selected = in_array($subject_id, $teaching_subjects) ? "selected" : "";
          ?>
            <option value="<?= $subject_id ?>" <?= $selected ?>><?= $subject_name ?></option>
          <?php endforeach; ?>
        </select>
      </td>
      <td>
        <button type="submit" name="operation[delete]" value="<?= $id ?>" class="edit option-button">Delete</button>
      </td>
    </tr>
  <?php endforeach; ?>

  <!-- ADD -->
  <tr>
    <td><input type="text" name="operation[add][name]" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="edit"></td>
    <td><input type="text" name="operation[add][surname]" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="edit"></td>
    <td><input type="email" name="operation[add][email]" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="edit"></td>
    <td><input type="number" name="operation[add][phone_number]" autocomplete="off" autocorrect="off" class="edit"></td>
    <td><select name="operation[add][teaching_subjects][]" size="2" class="edit" multiple>
        <?php foreach ($this->subjects as $subject_row):
          $subject_id = $subject_row["id"];
          $subject_name = $subject_row["name"];
        ?>
          <option value="<?= $subject_id ?>"><?= $subject_name ?></option>
        <?php endforeach; ?>
      </select>
    </td>
    <td><button type="submit" name="operation[add][confirm]" class="edit option-button">Add</button></td>
  </tr>
</table>

<!-- SCRIPT LOADING -->
<script src="assets/js/Common/Scroll.js"></script>
<script src="assets/js/Admin/EditTeachers.js"></script>