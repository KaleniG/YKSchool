document.addEventListener("DOMContentLoaded", () => {
  // SAVE LOGIC (STYLE + FUNCTIONALITY)
  const rows = document.querySelectorAll<HTMLTableElement>("table.edit tr[data-id]");

  rows.forEach(row => {
    const courseId = row.dataset.id;
    const descriptionTextarea = row.querySelector<HTMLTextAreaElement>(`textarea[name='operation[save][${courseId}][description]']`);
    const cell = row.querySelector<HTMLTableRowElement>("td:last-child");
    if (!courseId || !descriptionTextarea || !cell) return;

    const saveBtn = document.createElement("button");
    saveBtn.type = "button";
    saveBtn.className = "edit option-button save";
    saveBtn.textContent = "Save";

    const showSave = () => {
      if (!cell.contains(saveBtn)) {
        cell.appendChild(saveBtn);
        requestAnimationFrame(() => saveBtn.classList.add("visible"));
      }
    }

    const sendData = async () => {
      const formData = new FormData();
      formData.append(`operation[save][${courseId}][description]`, descriptionTextarea.value);
      formData.append("operation[save][confirm]", courseId);

      try {
        await fetch("teacher.php", { method: "POST", body: formData });
      } catch (err) {
        console.error("Failed to save the course data:", err);
      }

      if (saveBtn.isConnected) {
        requestAnimationFrame(() => saveBtn.classList.remove("visible"));
        setTimeout(() => saveBtn.remove(), 400);
      }
    }

    descriptionTextarea.addEventListener("input", showSave);
    saveBtn.addEventListener("click", sendData);
  });
});
