<table class="edit">
  <tr>
    <th>Name</th>
    <th>Description</th>
    <th></th>
  </tr>
  <?php foreach ($this->courses as $course):
    $id = $course["id"];
    $name = $course["name"];
    $description = $course["description"];
  ?>
    <tr>
      <td><input type="text" value="<?= $name ?>" class="edit" disabled></td>
      <td><textarea name='operation[save][<?= $id ?>][description]' class="edit"><?= $description ?></textarea></td>
      <td>
        <script>
          (function() {
            const row = document.currentScript.parentNode.parentNode; // <tr>
            const descriptionTextarea = row.querySelector('textarea[name="operation[save][<?= $id ?>][description]"]');

            const saveBtn = document.createElement('button');
            saveBtn.type = 'button';
            saveBtn.className = "edit-option-button-add";
            saveBtn.textContent = 'Save';

            function showSave() {
              const cell = descriptionTextarea.closest('tr').querySelector('td:last-child');
              if (!cell.contains(saveBtn)) {
                cell.appendChild(saveBtn);
                requestAnimationFrame(() => {
                  saveBtn.classList.add('visible');
                });
              }
            }

            async function sendData() {
              const formData = new FormData();

              formData.append("operation[save][<?= $id ?>][description]", descriptionTextarea.value);
              formData.append("operation[save][confirm]", "<?= $id ?>");

              try {
                const response = await fetch("teacher.php", {
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

            descriptionTextarea.addEventListener('input', showSave);
            saveBtn.addEventListener('click', sendData);
          })();
        </script>
      </td>
    </tr>
  <?php endforeach; ?>
</table>