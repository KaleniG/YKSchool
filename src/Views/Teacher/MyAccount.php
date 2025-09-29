<?php
$id = $this->user["id"];
$name = $this->user["name"];
$surname = $this->user["surname"];
$email = $this->user["email"];
$phone_number = $this->user["phone_number"];
$teaching_subjects = $this->user["teaching_subjects"];
?>

<div class="account-table">
  <label>Name:</label>
  <input type="text" value="<?= $name ?>" disabled>
  <br>
  <label>Surname:</label>
  <input type="text" value="<?= $surname ?>" disabled>
  <br>
  <label>E-mail:</label>
  <input type='text' name='operation[save][<?= $id ?>][email]' value='<?= $email ?>'>
  <br>
  <label>Phone Number:</label>
  <input type='text' name='operation[save][<?= $id ?>][phone_number]' value='<?= $phone_number ?>'>
  <br>
  <label>Teaching Subjects:</label>
  <select name='operation[save][<?= $id ?>][teaching_subjects][]' class='teacher-subjects' size='4' multiple>
    <?php foreach ($this->subjects as $subject_row):
      $subject_id = $subject_row['id'];
      $subject_name = $subject_row['name'];
      $selected = in_array($subject_id, $teaching_subjects) ? "selected" : "";
    ?>
      <option value='<?= $subject_id ?>' <?= $selected ?>><?= $subject_name ?></option>
    <?php endforeach ?>
  </select>
  <br>
</div>
<script>
  (function() {
    const container = document.currentScript.previousElementSibling; // .account-table
    const emailInput = container.querySelector('input[name="operation[save][<?= $id ?>][email]"]');
    const phoneInput = container.querySelector('input[name="operation[save][<?= $id ?>][phone_number]"]');
    const teachingSubjectsInput = container.querySelector('select[name="operation[save][<?= $id ?>][teaching_subjects][]"]');

    const saveBtn = document.createElement('button');
    saveBtn.type = 'submit';
    saveBtn.name = 'operation[save][confirm]';
    saveBtn.value = '<?= $id ?>';
    saveBtn.className = 'nav-button';
    saveBtn.textContent = 'Save Changes';

    function showSave() {
      if (!saveBtn.isConnected) {
        container.insertAdjacentElement('afterend', saveBtn);
      }
    }

    emailInput.addEventListener('input', showSave);
    phoneInput.addEventListener('input', showSave);
    teachingSubjectsInput.addEventListener('change', showSave);
  })();
</script>