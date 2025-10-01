<table class="edit">
  <tr>
    <th>Name</th>
    <th>Description</th>
    <th>Subscribed</th>
    <th></th>
  </tr>
  <?php
  foreach ($this->courses as $course):
    $id = $course["id"];
    $name = $course["name"];
    $description = $course["description"];
    $checked = $course["is_student_subscribed"] ? "checked" : "";
  ?>
    <tr>
      <td><input type='text' value='<?= $name ?>' class="edit" disabled></td>
      <td><input type='text' value='<?= $description ?>' class="edit" disabled></td>
      <td><input type='checkbox' name='operation[save][<?= $id ?>][is_student_subscribed]' value="t" class="edit" <?= $checked ?>></td>
      <td>
        <script>
          (function() {
            const row = document.currentScript.closest('tr');
            const checkbox = row.querySelector('input[name="operation[save][<?= $id ?>][is_student_subscribed]"]');
            const cell = document.currentScript.parentNode;

            const saveBtn = document.createElement('button');
            saveBtn.type = 'button';
            saveBtn.className = "edit-option-button-add"
            saveBtn.textContent = 'Save';

            function showSave() {
              if (!cell.contains(saveBtn)) {
                cell.appendChild(saveBtn);
                requestAnimationFrame(() => {
                  saveBtn.classList.add('visible');
                });
              }
            }

            async function sendData() {
              const formData = new FormData();

              if (checkbox.checked) {
                formData.append("operation[save][<?= $id ?>][is_student_subscribed]", "t");
              } else {
                formData.append("operation[save][<?= $id ?>][is_student_subscribed]", "f");
              }
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
                setTimeout(() => saveBtn.remove(), 400);
              }
            }

            checkbox.addEventListener('change', showSave);
            saveBtn.addEventListener('click', sendData);
          })();
        </script>
      </td>
    </tr>
  <?php endforeach; ?>
</table>