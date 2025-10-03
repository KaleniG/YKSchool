<table class="edit">
  <tr>
    <th>Name</th>
    <th>Description</th>
    <th>Subscribed</th>
    <th>Save</th>
  </tr>
  <?php
  foreach ($this->courses as $course):
    $id = $course["id"];
    $name = $course["name"];
    $description = $course["description"];
    $checked = $course["is_student_subscribed"] ? "checked" : "";
  ?>
    <tr data-id="<?= $id ?>">
      <td><input type="text" value="<?= $name ?>" class="edit" disabled></td>
      <td><textarea class="edit" disabled><?= $description ?></textarea></td>
      <td><input type="checkbox" name="operation[save][<?= $id ?>][is_student_subscribed]" value="t" class="edit" <?= $checked ?>></td>
      <td></td>
    </tr>
  <?php endforeach; ?>
</table>

<!-- SCRIPTS LOADING -->
<script src="assets/js/Common/Scroll.js"></script>
<script src="assets/js/Student/EditCourses.js"></script>