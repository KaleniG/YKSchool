document.addEventListener("DOMContentLoaded", () => {
  // SAVE LOGIC (STYLE + FUNCTIONALITY)
  const rows = document.querySelectorAll<HTMLTableElement>("table.edit tr[data-id]");
  if (!rows) return;

  rows.forEach(row => {
    const checkbox = row.querySelector<HTMLInputElement>("input[type='checkbox'][name^='operation[save]']");
    const lastCell = row.querySelector<HTMLTableCellElement>("td:last-child");
    if (!checkbox || !lastCell) return;

    const saveBtn = document.createElement("button");
    saveBtn.type = "button";
    saveBtn.className = "edit option-button save";
    saveBtn.textContent = "Save";

    const showSave = () => {
      if (!lastCell.contains(saveBtn)) {
        lastCell.appendChild(saveBtn);
        requestAnimationFrame(() => saveBtn.classList.add("visible"));
      }
    }

    const sendData = async () => {
      const formData = new FormData();
      const nameMatch = checkbox.name.match(/operation\[save\]\[(\d+)\]\[is_student_subscribed\]/);
      const id = nameMatch ? nameMatch[1] : null;
      if (!id) return;

      formData.append(`operation[save][${id}][is_student_subscribed]`, checkbox.checked ? "t" : "f");
      formData.append(`operation[save][confirm]`, id);

      try {
        await fetch("student.php", { method: "POST", body: formData });
      } catch (err) {
        console.error("Failed to save the user data:", err);
      }

      if (saveBtn.isConnected) {
        requestAnimationFrame(() => saveBtn.classList.remove("visible"));
        setTimeout(() => saveBtn.remove(), 400);
      }
    }

    checkbox.addEventListener("change", showSave);
    saveBtn.addEventListener("click", sendData);
  });
});
