<table class="edit">
  <tr>
    <th>Name</th>
    <th>Surname</th>
    <th>E-mail</th>
    <th>Phone Number</th>
    <th></th>
  </tr>

  <!-- UPDATE/DELETE -->
  <?php foreach ($this->admins as $admin_row):
    $id = $admin_row["id"];
    $name = $admin_row["name"];
    $surname = $admin_row["surname"];
    $email = $admin_row["email"];
    $phone = $admin_row["phone_number"];
  ?>
    <tr data-id="<?= $id ?>">
      <td><input type="text" value="<?= $name ?>" class="edit" disabled></td>
      <td><input type="text" value="<?= $surname ?>" class="edit" disabled></td>
      <td><input type="email" name="operation[save][<?= $id ?>][email]" value="<?= $email ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="edit"></td>
      <td><input type="number" name="operation[save][<?= $id ?>][phone_number]" value="<?= $phone ?>" autocomplete="off" autocorrect="off" class="edit"></td>
      <td>
        <?php if ($this->user["id"] != $id): ?>
          <button type="submit" name="operation[delete]" value="<?= $id ?>" class="edit option-button">Delete</button>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>

  <!-- INSERT -->
  <tr>
    <td><input type="text" name="operation[add][name]" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="edit"></td>
    <td><input type="text" name="operation[add][surname]" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="edit"></td>
    <td><input type="email" name="operation[add][email]" autocomplete="off" autocapitalize="off" spellcheck="false" class="edit"></td>
    <td><input type="number" name="operation[add][phone_number]" autocomplete="off" autocorrect="off" class="edit"></td>
    <td><button type="submit" name="operation[add][confirm]" class="edit option-button">Add</button></td>
  </tr>

</table>

<!-- SCRIPT LOADING -->
<script src="assets/js/Admin/EditAdmins.js"></script>