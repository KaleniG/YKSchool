const confirmButton = document.querySelector("button[name='operation[add][confirm]']");
const nameInput = document.querySelector("input[name='operation[add][name]']");

confirmButton.addEventListener("click", (event) => {
  nameInput.required = true;

  setTimeout(() => {
    nameInput.required = false;
  }, 0);
});

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("table.edit tr[data-id]").forEach((row) => {
    const id = row.dataset.id;
    const nameInput = row.querySelector(`input[name="operation[save][${id}][name]"]`);

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
      formData.append("operation[save][confirm]", id);

      try {
        await fetch("admin.php", {
          method: "POST",
          body: formData
        });
      } catch (err) {
        console.error("Failed to save the subject data:", err);
      }

      saveBtn.classList.remove("visible");
      saveBtn.disabled = true;
      setTimeout(() => saveBtn.remove(), 400);
    }

    nameInput.addEventListener("input", showSave);
    saveBtn.addEventListener("click", sendData);
  });
});
