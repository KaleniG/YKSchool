<table class="edit">
  <tr>
    <th>Subject</th>
    <th>Operations</th>
  </tr>

  <!-- UPDATE/DELETE -->
  <?php foreach ($this->subjects as $subject_row):
    $id = $subject_row["id"];
    $name = $subject_row["name"];
  ?>
    <tr data-id="<?= $id ?>">
      <td><input type="text" name="operation[save][<?= $id ?>][name]" value="<?= $name ?>" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="edit"></td>
      <td><button type="submit" name="operation[delete]" value="<?= $id ?>" class="edit option-button">Delete</button></td>
    </tr>
  <?php endforeach; ?>

  <!-- INSERT -->
  <tr>
    <td><input type="text" name="operation[add][name]" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="edit"></td>
    <td><button type="submit" name="operation[add][confirm]" class="edit option-button">Add</button></td>
  </tr>
</table>

<!-- SCRIPT LOADING -->
<script src="assets/js/Admin/EditSubjects.js"></script>