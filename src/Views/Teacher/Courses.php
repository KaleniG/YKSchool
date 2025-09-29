<table>
  <tr>
    <th>Name</th>
    <th>Description</th>
    <th style="min-width: 90px;"></th>
  </tr>
  <?php foreach ($this->courses as $course):
    $id = $course["id"];
    $name = $course["name"];
    $description = $course["description"];
  ?>
    <tr>
      <td><?= $name ?></td>
      <td><textarea name='operation[save][<?= $id ?>][description]' class='teacher-course-textarea'><?= $description ?></textarea></td>
      <td>
        <script>
          (function() {
            const row = document.currentScript.parentNode.parentNode; // <tr>
            const descriptionTextarea = row.querySelector('textarea[name="operation[save][<?= $id ?>][description]"]');

            const saveBtn = document.createElement('button');
            saveBtn.type = 'submit';
            saveBtn.name = 'operation[save][confirm]';
            saveBtn.value = '<?= $id ?>';
            saveBtn.className = 'nav-button';
            saveBtn.textContent = 'Save';

            function showSave() {
              const cell = descriptionTextarea.closest('tr').querySelector('td:last-child');
              if (!cell.contains(saveBtn)) {
                cell.appendChild(saveBtn);
              }
            }

            descriptionTextarea.addEventListener('input', showSave);
          })();
        </script>
      </td>
    </tr>
  <?php endforeach; ?>
</table>