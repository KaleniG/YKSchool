<table class="edit">
  <tr>
    <th>Name</th>
    <th>Description</th>
    <th>Save</th>
  </tr>
  <?php foreach ($this->courses as $course):
    $id = $course["id"];
    $name = $course["name"];
    $description = $course["description"];
  ?>
    <tr data-id="<?= $id ?>">
      <td><input type="text" value="<?= $name ?>" class="edit" disabled></td>
      <td><textarea name="operation[save][<?= $id ?>][description]" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="edit"><?= $description ?></textarea></td>
      <td></td>
    </tr>
  <?php endforeach; ?>
</table>

<!-- SCRIPTS LOADING -->
<script src="assets/js/Common/Scroll.js"></script>
<script src="assets/js/Teacher/EditCourses.js"></script>