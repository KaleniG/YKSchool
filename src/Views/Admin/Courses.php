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
    <tr>
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
      <td>
        <button type="submit" name="operation[delete]" value="<?= $id ?>" class="edit option-button">Delete</button>
        <script>
          (function() {
            const row = document.currentScript.parentNode.parentNode;
            const nameInput = row.querySelector("input[name='operation[save][<?= $id ?>][name]']");
            const descriptionInput = row.querySelector("textarea[name='operation[save][<?= $id ?>][description]']");
            const statusInput = row.querySelector("select[name='operation[save][<?= $id ?>][status]']");
            const teachersInput = row.querySelector("select[name='operation[save][<?= $id ?>][course_teachers][]']");
            const studentsInput = row.querySelector("select[name='operation[save][<?= $id ?>][course_students][]']");

            const saveBtn = document.createElement("button");
            saveBtn.type = "button";
            saveBtn.className = "edit option-button save"
            saveBtn.textContent = "Save";

            function showSave() {
              const cell = studentsInput.closest("tr").querySelector("td:last-child");
              if (!cell.contains(saveBtn)) {
                cell.appendChild(saveBtn);
                requestAnimationFrame(() => {
                  saveBtn.classList.add("visible");
                });
              }
            }

            async function sendData() {
              const formData = new FormData();

              formData.append("operation[save][<?= $id ?>][name]", nameInput.value);
              formData.append("operation[save][<?= $id ?>][description]", descriptionInput.value);
              formData.append("operation[save][<?= $id ?>][status]", statusInput.value);

              for (const option of teachersInput.selectedOptions) {
                formData.append("operation[save][<?= $id ?>][course_teachers][]", option.value);
              }

              for (const option of studentsInput.selectedOptions) {
                formData.append("operation[save][<?= $id ?>][course_students][]", option.value);
              }

              formData.append("operation[save][confirm]", "<?= $id ?>");

              try {
                const response = await fetch("admin.php", {
                  method: "POST",
                  body: formData
                });

              } catch (err) {
                console.error("Failed to save the course data: ", err);
              }

              if (saveBtn.isConnected) {
                requestAnimationFrame(() => {
                  saveBtn.classList.remove("visible");
                });
                setTimeout(() => saveBtn.remove(), 400);
              }
            }

            nameInput.addEventListener("input", showSave);
            descriptionInput.addEventListener("input", showSave);
            statusInput.addEventListener("change", showSave);
            teachersInput.addEventListener("change", showSave);
            studentsInput.addEventListener("change", showSave);
            saveBtn.addEventListener("click", sendData);
          })();
        </script>
      </td>
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
  <script type="module">
    const confirmButton = document.querySelector("button[name='operation[add][confirm]']");
    const nameInput = document.querySelector("input[name='operation[add][name]']");
    const statusSelect = document.querySelector("select[name='operation[add][status]']");
    const subjectSelect = document.querySelector("select[name='operation[add][subject]']");
    const teachersSelect = document.querySelector("select[name='operation[add][teachers][]']");

    confirmButton.addEventListener("click", (event) => {
      nameInput.required = true;
      statusSelect.required = true;
      subjectSelect.required = true;

      setTimeout(() => {
        nameInput.required = false;
        statusSelect.required = false;
        subjectSelect.required = false;
      }, 0);
    });

    function fetchTeachers() {
      return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "admin.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        xhr.onreadystatechange = function() {
          if (xhr.readyState === 4) {
            if (xhr.status === 200) {
              try {
                const data = JSON.parse(xhr.responseText);
                resolve(data);
              } catch (e) {
                reject(new Error("Invalid JSON: " + xhr.responseText));
              }
            } else {
              reject(new Error("Request failed with status " + xhr.status));
            }
          }
        };

        xhr.send();
      });
    }

    subjectSelect.addEventListener("change", async function() {
      const subjectId = Number(this.value);;
      teachersSelect.innerHTML = "";

      if (!subjectId) return;

      try {
        const teachers = await fetchTeachers();
        console.log(teachers);
        console.log(subjectId);
        teachers.forEach(t => {
          if (t.teaching_subjects.includes(subjectId)) {
            console.log("dsafdfsgdsf");
            const opt = document.createElement("option");
            opt.value = t.id;
            opt.textContent = t.name + " " + t.surname;
            teachersSelect.appendChild(opt);
          }
        });
      } catch (e) {
        console.error("Error fetching teachers:", e);
      }
    });
  </script>
</table>