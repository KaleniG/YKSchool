<tr>
  <th>Subject</th>
  <th></th>
</tr>


<!-- UPDATE/DELETE -->
<?php foreach ($this->subjects as $subject_row):
  $id = $subject_row["id"];
  $name = $subject_row["name"];
?>
  <tr>
    <td><input type="text" name="operation[save][<?= $id ?>][name]" value="<?= $name ?>" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="edit"></td>
    <td>
      <button type="submit" name="operation[delete]" value="<?= $id ?>" class="edit option-button">Delete</button>
      <script>
        (function() {
          const row = document.currentScript.parentNode.parentNode;
          const nameInput = row.querySelector("input[name='operation[save][<?= $id ?>][name]']");

          const saveBtn = document.createElement("button");
          saveBtn.type = "button";
          saveBtn.className = "edit option-button save";
          saveBtn.textContent = "Save";

          function showSave() {
            const cell = row.querySelector("td:last-child");
            if (!cell.contains(saveBtn)) {
              cell.appendChild(saveBtn);
              requestAnimationFrame(() => {
                saveBtn.classList.add("visible");
              });
            }
          }

          async function sendData() {
            const formData = new FormData();
            formData.append("operation[save][<?= $id ?>][name]", nameInput.value);
            formData.append("operation[save][confirm]", "<?= $id ?>");

            try {
              await fetch("admin.php", {
                method: "POST",
                body: formData
              });
            } catch (err) {
              console.error("Failed to save the course data:", err);
            }

            saveBtn.classList.remove("visible");
            saveBtn.disabled = true;
            setTimeout(() => saveBtn.remove(), 400);
          }

          nameInput.addEventListener("input", showSave);
          saveBtn.addEventListener("click", sendData);
        })();
      </script>
    </td>
  </tr>
<?php endforeach; ?>

<!-- INSERT -->
<tr>
  <td><input type="text" name="operation[add][name]" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="edit"></td>
  <td><button type="submit" name="operation[add][confirm]" class="edit option-button">Add</button></td>
</tr>
<script>
  const confirmButton = document.querySelector("button[name='operation[add][confirm]']");
  const nameInput = document.querySelector("input[name='operation[add][name]']");

  confirmButton.addEventListener("click", (event) => {
    nameInput.required = true;

    setTimeout(() => {
      nameInput.required = false;
    }, 0);
  });
</script>