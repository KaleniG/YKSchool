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
  <label for="email" class="edit">E-mail:
    <input type='text' name='operation[save][<?= $id ?>][email]' value='<?= $email ?>' id="email" class="edit">
  </label>
  <label for="phone_number" class="edit">Phone Number:
    <input type='text' name='operation[save][<?= $id ?>][phone_number]' value='<?= $phone_number ?>' id="phone_number" class="edit">
  </label>
  <label class="edit">Tution Status:
    <input type='text' name='operation[save][<?= $id ?>][tuition_enabled]' value='<?= $tuition_enabled ?>' class="edit" disabled>
  </label>
</div>
<script>
  (function() {
    const container = document.currentScript.previousElementSibling;
    const emailInput = container.querySelector('input[name="operation[save][<?= $id ?>][email]" ]');
    const phoneInput = container.querySelector('input[name="operation[save][<?= $id ?>][phone_number]" ]');

    const saveBtn = document.createElement('button');
    saveBtn.type = 'button';
    saveBtn.className = "edit-account-option-button-add"
    saveBtn.textContent = 'Save Changes';

    if (!saveBtn.isConnected) {
      container.insertAdjacentElement('afterend', saveBtn);
    }

    function showSave() {
      if (saveBtn.isConnected) {
        requestAnimationFrame(() => {
          saveBtn.classList.add('visible');
        });
      }
    }

    async function sendData() {
      const formData = new FormData();

      formData.append("operation[save][<?= $id ?>][email]", emailInput.value);
      formData.append("operation[save][<?= $id ?>][phone_number]", phoneInput.value);
      formData.append("operation[save][confirm]", "<?= $id ?>");

      try {
        const response = await fetch("student.php", {
          method: "POST",
          body: formData
        });

      } catch (err) {
        console.error("Failed to save the user data: ", err);
      }

      if (saveBtn.isConnected) {
        requestAnimationFrame(() => {
          saveBtn.classList.remove('visible');
        });
      }
    }

    emailInput.addEventListener('input', showSave);
    phoneInput.addEventListener('input', showSave);
    saveBtn.addEventListener('click', sendData);
  })();
</script>