<table class="edit">
  <tr>
    <th>Name</th>
    <th>Description</th>
    <th>Status</th>
    <th>Subject</th>
    <th>Teachers</th>
    <th>Students</th>
    <th></th>
  </tr>

  <!-- UPDATE/DELETE -->
  <?php foreach ($this->courses as $course_row):
    $id = $course_row["id"];
    $name = $course_row["name"];
    $description = $course_row["description"];
    $status = $course_row["status"];
    $course_subject_id = $course_row["subject"];
    $course_students = $course_row["course_students"];
    $course_teachers = $course_row["course_teachers"];
  ?>
    <tr data-id="<?= $id ?>">
      <td><input type="text" name="operation[save][<?= $id ?>][name]" value="<?= $name ?>" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="edit"></td>
      <td><textarea name="operation[save][<?= $id ?>][description]" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="edit"><?= $description ?></textarea></td>
      <td>
        <select name="operation[save][<?= $id ?>][status]" class="edit">
          <option value="Active" <?= (($status == "Active") ? "selected" : "") ?>>Active</option>
          <option value="Suspended" <?= (($status == "Suspended") ? "selected" : "") ?>>Suspended</option>
          <option value="UnderDevelopment" <?= (($status == "UnderDevelopment") ? "selected" : "") ?>>Under Development</option>
        </select>
      </td>
      <td>
        <?php foreach ($this->subjects as $subject_row):
          $subject_name = $subject_row["name"];
          $subject_id = $subject_row["id"];
        ?>
          <?php if ($course_subject_id == $subject_id): ?>
            <input type="hidden" name="operation[save][<?= $id ?>][subject]" value="<?= $subject_id ?>" class="edit">
            <input type="text" value="<?= $subject_name ?>" class="edit" disabled>
            <?php break; ?>
          <?php endif; ?>
        <?php endforeach; ?>
      </td>
      <td>
        <select name="operation[save][<?= $id ?>][course_teachers][]" size="4" class="edit" multiple>
          <?php foreach ($this->teachers as $teacher_row):
            $teacher_id = $teacher_row["id"];
            $teacher_name = $teacher_row["name"];
            $teacher_surname = $teacher_row["surname"];
            $teacher_teaching_subjects = $teacher_row["teaching_subjects"];
            $selected = (in_array($teacher_id, $course_teachers)) ? "selected" : "";
          ?>
            <?php if (in_array($course_subject_id, $teacher_teaching_subjects)): ?>
              <option value="<?= $teacher_id ?>" <?= $selected ?>><?= $teacher_name . " " . $teacher_surname ?></option>
            <?php endif; ?>
          <?php endforeach; ?>
        </select>
      </td>
      <td>
        <select name="operation[save][<?= $id ?>][course_students][]" size="4" class="edit" multiple>
          <?php foreach ($this->students as $student_row):
            $student_id = $student_row["id"];
            $student_name = $student_row["name"];
            $student_surname = $student_row["surname"];
            $student_tuition_enabled = ($student_row["tuition_enabled"] == "t") ? true : false;
            $selected = (in_array($student_id, $course_students)) ? "selected" : "";
          ?>
            <?php if ($student_tuition_enabled): ?>
              <option value="<?= $student_id ?>" <?= $selected ?>><?= $student_name . " " . $student_surname ?></option>
            <?php endif; ?>
          <?php endforeach; ?>
        </select>
      </td>
      <td><button type="submit" name="operation[delete]" value="<?= $id ?>" class="edit option-button">Delete</button></td>
    </tr>
  <?php endforeach; ?>

  <!-- ADD -->
  <tr>
    <td><input type="text" name="operation[add][name]" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="edit"></td>
    <td><textarea name="operation[add][description]" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="edit"></textarea></td>
    <td>
      <select name="operation[add][status]" class="edit">
        <option value="" disabled selected>Choose a status</option>
        <option value="Active">Active</option>
        <option value="Suspended">Suspended</option>
        <option value="UnderDevelopment">Under Development</option>
      </select>
    </td>
    <td>
      <select name="operation[add][subject]" class="edit">
        <option value="" disabled selected>Choose a subject</option>
        <?php foreach ($this->subjects as $subject_row):
          $subject_name = $subject_row["name"];
          $subject_id = $subject_row["id"];
          $selected = ($this->new_course_subject_selection == $subject_id) ? "selected" : "";
        ?>
          <option value="<?= $subject_id ?>" <?= $selected ?>><?= $subject_name ?></option>
        <?php endforeach; ?>
      </select>
    </td>
    <td>
      <select name="operation[add][teachers][]" size="2" class="edit" multiple>
      </select>
    </td>
    <td>
      <select name="operation[add][students][]" size="2" class="edit" multiple>
        <?php foreach ($this->students as $student_row):
          $student_id = $student_row["id"];
          $student_name = $student_row["name"];
          $student_surname = $student_row["surname"];
          $student_tuition_enabled = ($student_row["tuition_enabled"] == "t") ? true : false;
        ?>
          <?php if ($student_tuition_enabled): ?>
            <option value="<?= $student_id ?>"><?= $student_name . " " . $student_surname ?></option>
          <?php endif; ?>
        <?php endforeach; ?>
      </select>
    </td>
    <td>
      <button type="submit" name="operation[add][confirm]" class="edit option-button">Add</button>
    </td>
  </tr>
</table>

<!-- SCRIPT LOADING -->
<script src="assets/js/Common/Scroll.js"></script>
<script src="assets/js/Admin/EditCourses.js"></script>