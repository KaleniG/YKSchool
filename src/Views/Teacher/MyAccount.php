<?php
$id = $this->user["id"];
$name = $this->user["name"];
$surname = $this->user["surname"];
$email = $this->user["email"];
$phone_number = $this->user["phone_number"];
$teaching_subjects = $this->user["teaching_subjects"];
?>

<div class="edit-account">
  <label for="<?= $name ?>" class="edit">Name:
    <input type="text" value="<?= $name ?>" class="edit" disabled>
  </label>
  <label for="<?= $surname ?>" class="edit">Surname:
    <input type="text" value="<?= $surname ?>" class="edit" disabled>
  </label>
  <label for="operation[save][<?= $id ?>][email]" class="edit">E-mail:
    <input type='text' name='operation[save][<?= $id ?>][email]' value='<?= $email ?>' class="edit">
  </label>
  <label for="operation[save][<?= $id ?>][phone_number]" class="edit">Phone Number:
    <input type='text' name='operation[save][<?= $id ?>][phone_number]' value='<?= $phone_number ?>' class="edit">
  </label>
  <label for="operation[save][<?= $id ?>][teaching_subjects][]" class="edit">Teaching Subjects:
    <select name='operation[save][<?= $id ?>][teaching_subjects][]' size='4' class="edit" multiple>
      <?php foreach ($this->subjects as $subject_row):
        $subject_id = $subject_row['id'];
        $subject_name = $subject_row['name'];
        $selected = in_array($subject_id, $teaching_subjects) ? "selected" : "";
      ?>
        <option value='<?= $subject_id ?>' <?= $selected ?>><?= $subject_name ?></option>
      <?php endforeach ?>
    </select>
  </label>
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
    saveBtn.className = 'edit-account-option-button-add'
    saveBtn.textContent = 'Save Changes';

    function showSave() {
      if (!saveBtn.isConnected) {
        container.insertAdjacentElement('afterend', saveBtn);
        requestAnimationFrame(() => {
          saveBtn.classList.add('visible'); // trigger fade-in
        });
      }
    }

    emailInput.addEventListener('input', showSave);
    phoneInput.addEventListener('input', showSave);
    teachingSubjectsInput.addEventListener('change', showSave);
  })();
</script>