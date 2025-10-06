document.addEventListener("DOMContentLoaded", () => {

  // SAVE LOGIC (STYLE + FUNCTIONALITY)
  document.querySelectorAll<HTMLTableRowElement>("table.edit tr[data-id]").forEach((row) => {
    const id = row.dataset.id;
    if (!id) return;

    const nameInput = row.querySelector<HTMLInputElement>(`input[name="operation[save][${id}][name]"]`);
    if (!nameInput) return;

    const saveBtn = document.createElement("button");
    saveBtn.type = "button";
    saveBtn.className = "edit option-button save";
    saveBtn.textContent = "Save";

    const showSave = () => {
      const cell = row.querySelector("td:last-child");
      if (!cell || cell.contains(saveBtn)) return;
      cell.appendChild(saveBtn);
      requestAnimationFrame(() => saveBtn.classList.add("visible"));
    };

    const sendData = async () => {
      const formData = new FormData();
      formData.append(`operation[save][${id}][name]`, nameInput.value);
      formData.append("operation[save][confirm]", id);

      try {
        await fetch("admin.php", { method: "POST", body: formData });
      } catch (err) {
        console.error("Failed to save the subject data:", err);
      }

      if (saveBtn.isConnected) {
        requestAnimationFrame(() => saveBtn.classList.remove("visible"));
        setTimeout(() => saveBtn.remove(), 400);
      }
    };

    nameInput.addEventListener("input", showSave);
    saveBtn.addEventListener("click", sendData);
  });

  // ADD LOGIC (VALIDATION)
  const confirmButton = document.querySelector<HTMLButtonElement>("button[name='operation[add][confirm]']");
  const nameInput = document.querySelector<HTMLInputElement>("input[name='operation[add][name]']");

  confirmButton?.addEventListener("click", (event) => {
    if (nameInput) nameInput.required = true;

    setTimeout(() => {
      if (nameInput) nameInput.required = false;
    }, 0);
  });
});
