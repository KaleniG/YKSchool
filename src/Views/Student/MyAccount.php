<?php
$id = $this->user["id"];
$name = $this->user["name"];
$surname = $this->user["surname"];
$email = $this->user["email"];
$phone_number = $this->user["phone_number"];
$tuition_enabled = $this->user["tuition_enabled"] ? "Enabled" : "Disabled";
?>

<div>
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
  <label>Tution Status:</label>
  <input type='text' name='operation[save][<?= $id ?>][tuition_enabled]' value='<?= $tuition_enabled ?>' disabled>
  <br>
</div>
<script>
  (function() {
    const container = document.currentScript.previousElementSibling; // .account-table
    const emailInput = container.querySelector(' input[name="operation[save][<?= $id ?>][email]" ]');
    const phoneInput = container.querySelector('input[name="operation[save][<?= $id ?>][phone_number]" ]');

    const saveBtn = document.createElement('button');
    saveBtn.type = 'submit';
    saveBtn.name = 'operation[save][confirm]';
    saveBtn.value = '<?= $id ?>';
    saveBtn.textContent = 'Save Changes';

    function showSave() {
      if (!saveBtn.isConnected) {
        container.insertAdjacentElement('afterend', saveBtn);
      }
    }

    emailInput.addEventListener('input', showSave);
    phoneInput.addEventListener('input', showSave);
  })();
</script>