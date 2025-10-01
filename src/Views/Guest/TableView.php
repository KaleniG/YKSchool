<table class="present">
  <tr>
    <th>Name</th>
    <th>Description</th>
    <th>Subject</th>
  </tr>
  <?php foreach ($this->courses as $course):
    $name = $course["name"];
    $description = $course["description"];
    $subject = $course["subject"];
  ?>
    <tr>
      <td><input type='text' value='<?= $name ?>' class="present" disabled></td>
      <td><textarea class="present" disabled><?= $description ?></textarea></td>
      <td><input type='text' value='<?= $subject ?>' class="present" disabled></td>
    </tr>
  <?php endforeach; ?>
</table>