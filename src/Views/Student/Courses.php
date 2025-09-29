<table>
  <tr>
    <th>Name</th>
    <th>Description</th>
    <th>Subscribed</th>
    <th style="min-width: 90px;"></th>
  </tr>
  <?php
  foreach ($this->courses as $course):
    $id = $course["id"];
    $name = $course["name"];
    $description = $course["description"];
    $checked = $course["is_student_subscribed"] ? "checked" : "";
  ?>
    <tr>
      <td><?= $name ?></td>
      <td><?= $description ?></td>
      <td><input type='checkbox' name='operation[save][<?= $id ?>][is_student_subscribed]' value="t" <?= $checked ?>></td>
      <td>
        <script>
          (function() {
            const row = document.currentScript.closest('tr');
            const checkbox = row.querySelector('input[name="operation[save][<?= $id ?>][is_student_subscribed]"]');
            const cell = document.currentScript.parentNode;

            const saveBtn = document.createElement('button');
            saveBtn.type = 'submit';
            saveBtn.name = 'operation[save][confirm]';
            saveBtn.value = '<?= $id ?>';
            saveBtn.className = 'nav-button';
            saveBtn.textContent = 'Save';

            function showSave() {
              if (!cell.contains(saveBtn)) {
                cell.appendChild(saveBtn);
              }
            }

            checkbox.addEventListener('change', showSave);
          })();
        </script>
      </td>
    </tr>
  <?php endforeach; ?>
</table>