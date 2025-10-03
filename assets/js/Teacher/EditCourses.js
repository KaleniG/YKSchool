document.addEventListener("DOMContentLoaded", () => {
  const rows = document.querySelectorAll("table.edit tr[data-id]");

  rows.forEach(row => {
    const courseId = row.dataset.id;
    const descriptionTextarea = row.querySelector(`textarea[name='operation[save][${courseId}][description]']`);
    const cell = row.querySelector("td:last-child");

    if (!descriptionTextarea || !cell) return;

    const saveBtn = document.createElement("button");
    saveBtn.type = "button";
    saveBtn.className = "edit option-button save";
    saveBtn.textContent = "Save";

    function showSave() {
      if (!cell.contains(saveBtn)) {
        cell.appendChild(saveBtn);
        requestAnimationFrame(() => saveBtn.classList.add("visible"));
      }
    }

    async function sendData() {
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
