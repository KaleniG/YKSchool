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

    xhr.onreadystatechange = function () {
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

window.addEventListener("DOMContentLoaded", async () => {
  const subjectId = Number(subjectSelect.value);
  teachersSelect.innerHTML = "";

  if (!subjectId) return;

  try {
    const teachers = await fetchTeachers();
    teachers.forEach(t => {
      if (t.teaching_subjects.includes(subjectId)) {
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

subjectSelect.addEventListener("change", async function () {
  const subjectId = Number(this.value);
  teachersSelect.innerHTML = "";

  if (!subjectId) return;

  try {
    const teachers = await fetchTeachers();
    teachers.forEach(t => {
      if (t.teaching_subjects.includes(subjectId)) {
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

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("table.edit tr[data-id]").forEach((row) => {
    const id = row.dataset.id;
    const nameInput = row.querySelector(`input[name="operation[save][${id}][name]"]`);
    const descriptionInput = row.querySelector(`textarea[name="operation[save][${id}][description]"]`);
    const statusInput = row.querySelector(`select[name="operation[save][${id}][status]"]`);
    const teachersInput = row.querySelector(`select[name="operation[save][${id}][course_teachers][]"]`);
    const studentsInput = row.querySelector(`select[name="operation[save][${id}][course_students][]"]`);

    const saveBtn = document.createElement("button");
    saveBtn.type = "button";
    saveBtn.className = "edit option-button save";
    saveBtn.textContent = "Save";

    function showSave() {
      const cell = row.querySelector("td:last-child");
      if (!cell.contains(saveBtn)) {
        cell.appendChild(saveBtn);
        requestAnimationFrame(() => saveBtn.classList.add("visible"));
      }
    }

    async function sendData() {
      const formData = new FormData();
      formData.append(`operation[save][${id}][name]`, nameInput.value);
      formData.append(`operation[save][${id}][description]`, descriptionInput.value);
      formData.append(`operation[save][${id}][status]`, statusInput.value);

      for (const option of teachersInput.selectedOptions) {
        formData.append(`operation[save][${id}][course_teachers][]`, option.value);
      }

      for (const option of studentsInput.selectedOptions) {
        formData.append(`operation[save][${id}][course_students][]`, option.value);
      }

      formData.append("operation[save][confirm]", id);

      try {
        await fetch("admin.php", { method: "POST", body: formData });
      } catch (err) {
        console.error("Failed to save the course data:", err);
      }

      if (saveBtn.isConnected) {
        requestAnimationFrame(() => saveBtn.classList.remove("visible"));
        setTimeout(() => saveBtn.remove(), 400);
      }
    }

    nameInput.addEventListener("input", showSave);
    descriptionInput.addEventListener("input", showSave);
    statusInput.addEventListener("change", showSave);
    teachersInput.addEventListener("change", showSave);
    studentsInput.addEventListener("change", showSave);
    saveBtn.addEventListener("click", sendData);
  });
});