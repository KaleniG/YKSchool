document.addEventListener("DOMContentLoaded", () => {
  const rows = document.querySelectorAll("table.edit tr[data-id]");

  rows.forEach(row => {
    const checkbox = row.querySelector("input[type='checkbox'][name^='operation[save]']");
    if (!checkbox) return;

    // Append the save button in the last cell of the row
    const lastCell = row.querySelector("td:last-child");

    const saveBtn = document.createElement("button");
    saveBtn.type = "button";
    saveBtn.className = "edit option-button save";
    saveBtn.textContent = "Save";

    function showSave() {
      if (!lastCell.contains(saveBtn)) {
        lastCell.appendChild(saveBtn);
        requestAnimationFrame(() => saveBtn.classList.add("visible"));
      }
    }

    async function sendData() {
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
