const confirmButton = document.querySelector("button[name='operation[add][confirm]']");
const nameInput = document.querySelector("input[name='operation[add][name]']");
const surnameInput = document.querySelector("input[name='operation[add][surname]']");

confirmButton.addEventListener("click", (event) => {
  nameInput.required = true;
  surnameInput.required = true;

  setTimeout(() => {
    nameInput.required = false;
    surnameInput.required = false;
  }, 0);
});

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("table.edit tr[data-id]").forEach((row) => {
    const id = row.dataset.id;
    const emailInput = row.querySelector(`input[name="operation[save][${id}][email]"]`);
    const phoneInput = row.querySelector(`input[name="operation[save][${id}][phone_number]"]`);
    const teachingSubjectsInput = row.querySelector(`select[name="operation[save][${id}][teaching_subjects][]"]`);

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
      formData.append(`operation[save][${id}][email]`, emailInput.value);
      formData.append(`operation[save][${id}][phone_number]`, phoneInput.value);

      for (const option of teachingSubjectsInput.selectedOptions) {
        formData.append(`operation[save][${id}][teaching_subjects][]`, option.value);
      }

      formData.append("operation[save][confirm]", id);

      try {
        await fetch("admin.php", {
          method: "POST",
          body: formData
        });
      } catch (err) {
        console.error("Failed to save the teacher data: ", err);
      }

      if (saveBtn.isConnected) {
        requestAnimationFrame(() => saveBtn.classList.remove("visible"));
        setTimeout(() => saveBtn.remove(), 400);
      }
    }

    emailInput.addEventListener("input", showSave);
    phoneInput.addEventListener("input", showSave);
    teachingSubjectsInput.addEventListener("change", showSave);
    saveBtn.addEventListener("click", sendData);
  });
});
