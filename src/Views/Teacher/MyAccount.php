<div class="account-table">
  <label>Name:</label><input type="text" value="<?= $this->user->name ?>" disabled>
  <br>
  <label>Surname:</label><input type="text" value="<?= $this->user->surname ?>" disabled>
  <br>
  <?php

  use App\Config\LogManager;

  $id = $this->current_table["teachers"]["id"];
  $email = $this->current_table["teachers"]["email"];
  $phone_number = $this->current_table["teachers"]["phone_number"];

  $subjects_table = $this->current_table["subjects"];
  $teaching_subjects = $this->current_table["teachers"]["teaching_subjects"];

  echo ("<label>E-mail:</label><input type='text' name='modified_table[{$id}][email]' value='{$email}'><br>");
  echo ("<label>Phone Number:</label><input type='text' name='modified_table[{$id}][phone_number]' value='{$phone_number}'><br>");
  echo ("<label>Teaching Subjects:</label><select name='modified_table[{$id}][teaching_subjects][]' class='teacher-subjects' multiple>");

  foreach ($subjects_table as $subject_row) {
    $subject_name = $subject_row['subject'];
    $subject_id = $subject_row['id'];
    $selected = in_array($subject_name, $teaching_subjects) ? "selected" : "";
    echo ("<option value='{$subject_id}' $selected>$subject_name</option>");
  }

  echo ("</select><br>");
  ?>
</div>
<button type='submit' name='operation' value='save_changes' class='nav-button'>Save Changes</button>