<?php
$id = $this->user["id"];
$name = $this->user["name"];
$surname = $this->user["surname"];
$email = $this->user["email"];
$phone_number = $this->user["phone_number"];
$tuition_enabled = $this->user["tuition_enabled"] ? "Enabled" : "Disabled";
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
  <label class="edit">Tution Status:
    <input type="text" name="operation[save][<?= $id ?>][tuition_enabled]" value="<?= $tuition_enabled ?>" class="edit" disabled>
  </label>
</div>

<!-- SCRIPTS LOADING -->
<script src="assets/js/Student/EditMyAccount.js"></script>