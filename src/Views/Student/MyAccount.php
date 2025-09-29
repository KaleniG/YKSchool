<?php
$id = $this->user["id"];
$name = $this->user["name"];
$surname = $this->user["surname"];
$email = $this->user["email"];
$phone_number = $this->user["phone_number"];
$tuition_enabled = $this->user["tuition_enabled"] ? "Enabled" : "Disabled";
?>

<div class="edit-account">
  <label class="edit">Name:
    <input type="text" value="<?= $name ?>" class="edit" disabled>
  </label>
  <label class="edit">Surname:
    <input type="text" value="<?= $surname ?>" class="edit" disabled>
  </label>
  <label class="edit">E-mail:
    <input type='text' name='operation[save][<?= $id ?>][email]' value='<?= $email ?>' class="edit">
  </label>
  <label class="edit">Phone Number:
    <input type='text' name='operation[save][<?= $id ?>][phone_number]' value='<?= $phone_number ?>' class="edit">
  </label>
  <label class="edit">Tution Status:
    <input type='text' name='operation[save][<?= $id ?>][tuition_enabled]' value='<?= $tuition_enabled ?>' class="edit" disabled>
  </label>
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
    saveBtn.className = "edit-account-option-button-add"
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
  })();
</script>