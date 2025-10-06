interface Teacher {
  id: string;
  name: string;
  surname: string;
  teaching_subjects: number[];
}

async function populateTeachers(subjectId: number, selectElement: HTMLSelectElement) {
  selectElement.innerHTML = "";
  if (!subjectId) return;

  try {
    const teachers: Teacher[] = await fetchTeachers();
    teachers
      .filter(t => t.teaching_subjects.includes(subjectId))
      .forEach(t => {
        const opt = document.createElement("option");
        opt.value = t.id;
        opt.textContent = `${t.name} ${t.surname}`;
        selectElement.appendChild(opt);
      });
  } catch (e) {
    console.error("Error fetching teachers:", e);
  }
}

async function fetchTeachers(): Promise<Teacher[]> {
  try {
    const response = await fetch("admin.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        "X-Requested-With": "XMLHttpRequest"
      },
      body: ""
    });

    if (!response.ok) {
      throw new Error(`Request failed with status ${response.status}`);
    }

    const data = await response.json();
    return data as Teacher[];
  } catch (err) {
    console.error("Failed to fetch teachers:", err);
    throw err;
  }
}

document.addEventListener("DOMContentLoaded", async () => {

  // SAVE LOGIC (STYLE + FUNCTIONALITY)
  document.querySelectorAll<HTMLTableRowElement>("table.edit tr[data-id]").forEach((row) => {
    const id = row.dataset.id;
    if (!id) return;

    const nameInput = row.querySelector<HTMLInputElement>(`input[name="operation[save][${id}][name]"]`);
    const descriptionInput = row.querySelector<HTMLTextAreaElement>(`textarea[name="operation[save][${id}][description]"]`);
    const statusInput = row.querySelector<HTMLSelectElement>(`select[name="operation[save][${id}][status]"]`);
    const teachersInput = row.querySelector<HTMLSelectElement>(`select[name="operation[save][${id}][course_teachers][]"]`);
    const studentsInput = row.querySelector<HTMLSelectElement>(`select[name="operation[save][${id}][course_students][]"]`);

    if (!nameInput
      || !descriptionInput
      || !statusInput
      || !teachersInput
      || !studentsInput
    ) return;

    const saveBtn = document.createElement("button");
    saveBtn.type = "button";
    saveBtn.className = "edit option-button save";
    saveBtn.textContent = "Save";

    const showSave = () => {
      const cell = row.querySelector<HTMLTableCellElement>("td:last-child");
      if (!cell || cell.contains(saveBtn)) return;
      cell.appendChild(saveBtn);
      requestAnimationFrame(() => saveBtn.classList.add("visible"));
    };

    const sendData = async () => {
      const formData = new FormData();
      formData.append(`operation[save][${id}][name]`, nameInput.value);
      formData.append(`operation[save][${id}][description]`, descriptionInput.value);
      formData.append(`operation[save][${id}][status]`, statusInput.value);

      Array.from(teachersInput.selectedOptions).forEach(option => {
        formData.append(`operation[save][${id}][course_teachers][]`, option.value);
      });

      Array.from(studentsInput.selectedOptions).forEach(option => {
        formData.append(`operation[save][${id}][course_students][]`, option.value);
      });

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
    };

    nameInput.addEventListener("input", showSave);
    descriptionInput.addEventListener("input", showSave);
    statusInput.addEventListener("change", showSave);
    teachersInput.addEventListener("change", showSave);
    studentsInput.addEventListener("change", showSave);
    saveBtn.addEventListener("click", sendData);
  });

  // ADD LOGIC (VALIDATION)
  const confirmButton = document.querySelector<HTMLButtonElement>("button[name='operation[add][confirm]']");
  const nameInput = document.querySelector<HTMLInputElement>("input[name='operation[add][name]']");
  const statusSelect = document.querySelector<HTMLSelectElement>("select[name='operation[add][status]']");
  const subjectSelect = document.querySelector<HTMLSelectElement>("select[name='operation[add][subject]']");

  confirmButton?.addEventListener("click", (event) => {
    if (nameInput) nameInput.required = true;
    if (statusSelect) statusSelect.required = true;
    if (subjectSelect) subjectSelect.required = true;

    setTimeout(() => {
      if (nameInput) nameInput.required = false;
      if (statusSelect) statusSelect.required = false;
      if (subjectSelect) subjectSelect.required = false;
    }, 0);
  });

  // ADD LOGIC (ON SUBJECT SELECT, LOAD TEACHERS LIST)
  const teachersSelect = document.querySelector<HTMLSelectElement>("select[name='operation[add][teachers][]']");

  if (subjectSelect && teachersSelect) {
    populateTeachers(Number(subjectSelect.value), teachersSelect);

    subjectSelect.addEventListener("change", () => {
      populateTeachers(Number(subjectSelect.value), teachersSelect);
    });
  }
});