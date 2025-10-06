document.addEventListener("DOMContentLoaded", () => {

  // SAVE LOGIC (STYLE + FUNCTIONALITY)
  document.querySelectorAll<HTMLTableElement>("table.edit tr[data-id]").forEach((row) => {
    const id = row.dataset.id;
    if (!id) return;

    const emailInput = row.querySelector<HTMLInputElement>(`input[name="operation[save][${id}][email]"]`);
    const phoneInput = row.querySelector<HTMLInputElement>(`input[name="operation[save][${id}][phone_number]"]`);
    const tuitionInput = row.querySelector<HTMLInputElement>(`input[name="operation[save][${id}][tuition_enabled]"]`);
    if (!emailInput || !phoneInput || !tuitionInput) return;

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
      formData.append(`operation[save][${id}][email]`, emailInput.value);
      formData.append(`operation[save][${id}][phone_number]`, phoneInput.value);
      formData.append(`operation[save][${id}][tuition_enabled]`, tuitionInput.checked ? "t" : "f");
      formData.append("operation[save][confirm]", id);

      try {
        await fetch("admin.php", { method: "POST", body: formData });
      } catch (err) {
        console.error("Failed to save the student data: ", err);
      }

      if (saveBtn.isConnected) {
        requestAnimationFrame(() => saveBtn.classList.remove("visible"));
        setTimeout(() => saveBtn.remove(), 400);
      }
    };

    emailInput.addEventListener("input", showSave);
    phoneInput.addEventListener("input", showSave);
    tuitionInput.addEventListener("input", showSave);
    saveBtn.addEventListener("click", sendData);
  });

  // ADD LOGIC (VALIDATION)
  const confirmButton = document.querySelector<HTMLButtonElement>("button[name='operation[add][confirm]']");
  const nameInput = document.querySelector<HTMLInputElement>("input[name='operation[add][name]']");
  const surnameInput = document.querySelector<HTMLInputElement>("input[name='operation[add][surname]']");

  confirmButton?.addEventListener("click", (event) => {
    if (nameInput) nameInput.required = true;
    if (surnameInput) surnameInput.required = true;

    setTimeout(() => {
      if (nameInput) nameInput.required = false;
      if (surnameInput) surnameInput.required = false;
    }, 0);
  });
});
