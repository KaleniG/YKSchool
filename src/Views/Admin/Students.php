<tr>
  <th>Name</th>
  <th>Surname</th>
  <th>E-mail</th>
  <th>Phone Number</th>
  <th>Tuition Enabled</th>
  <th></th>
</tr>

<!-- UPDATE/DELETE -->
<?php foreach ($this->students as $student_row):
  $id = $student_row["id"];
  $name = $student_row["name"];
  $surname = $student_row["surname"];
  $email = $student_row["email"];
  $phone_number = $student_row["phone_number"];
  $checked = ($student_row["tuition_enabled"] == "t") ? "checked" : "";
?>

  <tr>
    <td><input type="text" value="<?= $name ?>" class="edit" disabled></td>
    <td><input type="text" value="<?= $surname ?>" class="edit" disabled></td>
    <td><input type="email" name="operation[save][<?= $id ?>][email]" value="<?= $email ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="edit"></td>
    <td><input type="number" name="operation[save][<?= $id ?>][phone_number]" value="<?= $phone_number ?>" autocomplete="off" autocorrect="off" class="edit"></td>
    <td><input type="checkbox" name="operation[save][<?= $id ?>][tuition_enabled]" value="t" class="edit" <?= $checked ?>></td>
    <td>
      <button type="submit" name="operation[delete]" value="<?= $id ?>" class="edit option-button">Delete</button>
      <script>
        (function() {
          const row = document.currentScript.parentNode.parentNode;
          const emailInput = row.querySelector("input[name='operation[save][<?= $id ?>][email]']");
          const phoneInput = row.querySelector("input[name='operation[save][<?= $id ?>][phone_number]']");
          const tuitionInput = row.querySelector("input[name='operation[save][<?= $id ?>][tuition_enabled]']");

          const saveBtn = document.createElement("button");
          saveBtn.type = "button";
          saveBtn.className = "edit option-button save";
          saveBtn.textContent = "Save";

          function showSave() {
            const cell = tuitionInput.closest("tr").querySelector("td:last-child");
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
            if (tuitionInput.checked) {
              formData.append("operation[save][<?= $id ?>][tuition_enabled]", "t");
            } else {
              formData.append("operation[save][<?= $id ?>][tuition_enabled]", "f");
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
                saveBtn.classList.remove("visible");
              });
              setTimeout(() => saveBtn.remove(), 400);
            }
          }

          emailInput.addEventListener("input", showSave);
          phoneInput.addEventListener("input", showSave);
          tuitionInput.addEventListener("input", showSave);
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
  <td><input type="email" name="operation[add][email]" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="edit"></td>
  <td><input type="number" name="operation[add][phone_number]" autocomplete="off" autocorrect="off" class="edit"></td>
  <td><input type="checkbox" name="operation[add][tuition_enabled]" value="t" class="edit"></td>
  <td><button type="submit" name="operation[add][confirm]" class="edit option-button">Add</button></td>
</tr>