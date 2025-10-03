<?php
$id = $this->user["id"];
$name = $this->user["name"];
$surname = $this->user["surname"];
$email = $this->user["email"];
$phone_number = $this->user["phone_number"];
$teaching_subjects = $this->user["teaching_subjects"];
?>

<div class="edit account" data-user-id="<?= $id ?>">
  <label class="edit">Name:
    <input type="text" value="<?= $name ?>" class="edit" disabled>
  </label>
  <label class="edit">Surname:
    <input type="text" value="<?= $surname ?>" class="edit" disabled>
  </label>
  <label for="email" class="edit">E-mail:
    <input type="text" name="operation[save][<?= $id ?>][email]" value="<?= $email ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="email" class="edit">
  </label>
  <label for="phone_number" class="edit">Phone Number:
    <input type="number" name="operation[save][<?= $id ?>][phone_number]" value="<?= $phone_number ?>" autocomplete="off" autocorrect="off" id="phone_number" class="edit">
  </label>
  <label for="teaching_subjects" class="edit">Teaching Subjects:
    <select name="operation[save][<?= $id ?>][teaching_subjects][]" size="4" id="teaching_subjects" class="edit" multiple>
      <?php foreach ($this->subjects as $subject_row):
        $subject_id = $subject_row["id"];
        $subject_name = $subject_row["name"];
        $selected = in_array($subject_id, $teaching_subjects) ? "selected" : "";
      ?>
        <option value="<?= $subject_id ?>" <?= $selected ?>><?= $subject_name ?></option>
      <?php endforeach ?>
    </select>
  </label>
</div>

<!-- SCRIPTS LOADING -->
<script src="assets/js/Common/Scroll.js"></script>
<script src="assets/js/Teacher/EditMyAccount.js"></script>