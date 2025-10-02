<tr>
  <th>Name</th>
  <th>Surname</th>
  <th>E-mail</th>
  <th>Phone Number</th>
  <th></th>
</tr>

<!-- UPDATE/DELETE -->
<?php foreach ($this->admins as $admin_row):
  $id = $admin_row["id"];
  $name = $admin_row["name"];
  $surname = $admin_row["surname"];
  $email = $admin_row["email"];
  $phone = $admin_row["phone_number"];
?>
  <tr>
    <td><input type="text" value="<?= $name ?>" class="edit" disabled></td>
    <td><input type="text" value="<?= $surname ?>" class="edit" disabled></td>
    <td><input type="email" name="operation[save][<?= $id ?>][email]" value="<?= $email ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="edit"></td>
    <td><input type="number" name="operation[save][<?= $id ?>][phone_number]" value="<?= $phone ?>" autocomplete="off" autocorrect="off" class="edit"></td>
    <td>
      <?php if ($this->user["id"] != $id): ?>
        <button type="submit" name="operation[delete]" value="<?= $id ?>" class="edit option-button">Delete</button>
      <?php endif; ?>
      <script>
        (function() {
          const row = document.currentScript.parentNode.parentNode; // <tr>
          const emailInput = row.querySelector("input[name='operation[save][<?= $id ?>][email]']");
          const phoneInput = row.querySelector("input[name='operation[save][<?= $id ?>][phone_number]']");

          const saveBtn = document.createElement("button");
          saveBtn.type = "button";
          saveBtn.className = "edit option-button save";
          saveBtn.textContent = "Save";

          function showSave() {
            const cell = emailInput.closest("tr").querySelector("td:last-child");
            if (!cell.contains(saveBtn)) {
              cell.appendChild(saveBtn);
              requestAnimationFrame(() => {
                saveBtn.classList.add("visible");
              });
            }
          }

          async function sendData() {
            const formData = new FormData();

            formData.append("operation[save][<?= $id ?>][email]", emailInput.value);
            formData.append("operation[save][<?= $id ?>][phone_number]", phoneInput.value);
            formData.append("operation[save][confirm]", "<?= $id ?>");

            try {
              const response = await fetch("admin.php", {
                method: "POST",
                body: formData
              });

            } catch (err) {
              console.error("Failed to save the user data: ", err);
            }

            if (saveBtn.isConnected) {
              requestAnimationFrame(() => {
                saveBtn.classList.remove("visible");
              });
              setTimeout(() => saveBtn.remove(), 400);
            }
          }

          emailInput.addEventListener("input", showSave);
          phoneInput.addEventListener("input", showSave);
          saveBtn.addEventListener("click", sendData);
        })();
      </script>
    </td>
  </tr>
<?php endforeach; ?>


<!-- INSERT -->
<tr>
  <td><input type="text" name="operation[add][name]" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="edit"></td>
  <td><input type="text" name="operation[add][surname]" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="edit"></td>
  <td><input type="email" name="operation[add][email]" autocomplete="off" autocapitalize="off" spellcheck="false" class="edit"></td>
  <td><input type="number" name="operation[add][phone_number]" autocomplete="off" autocorrect="off" class="edit"></td>
  <td><button type="submit" name="operation[add][confirm]" class="edit option-button">Add</button></td>
</tr>
<script>
  const confirmButton = document.querySelector("button[name='operation[add][confirm]']");
  const nameInput = document.querySelector("input[name='operation[add][name]']");
  const surnameInput = document.querySelector("input[name='operation[add][surname]']");

  confirmButton.addEventListener("click", (event) => {
    nameInput.required = true;
    surnameInput.required = true;

    setTimeout(() => {
      nameInput.required = false;
      surnameInput.required = false;
    }, 0);
  });
</script>