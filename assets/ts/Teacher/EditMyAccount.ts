document.addEventListener("DOMContentLoaded", () => {
  // SAVE LOGIC (STYLE + FUNCTIONALITY)
  const container = document.querySelector<HTMLDivElement>(".edit.account");
  if (!container) return;

  const userId = container.dataset.userId;
  const emailInput = container.querySelector<HTMLInputElement>(`input[name='operation[save][${userId}][email]']`);
  const phoneInput = container.querySelector<HTMLInputElement>(`input[name='operation[save][${userId}][phone_number]']`);
  const teachingSubjectsInput = container.querySelector<HTMLSelectElement>(`select[name='operation[save][${userId}][teaching_subjects][]']`);
  if (!userId || !emailInput || !phoneInput || !teachingSubjectsInput) return;

  const saveBtn = document.createElement("button");
  saveBtn.type = "button";
  saveBtn.className = "edit account option-button save";
  saveBtn.textContent = "Save Changes";

  container.insertAdjacentElement("afterend", saveBtn);

  const showSave = () => {
    if (saveBtn.isConnected)
      requestAnimationFrame(() => saveBtn.classList.add("visible"));
  }

  const sendData = async () => {
    const formData = new FormData();
    formData.append(`operation[save][${userId}][email]`, emailInput.value);
    formData.append(`operation[save][${userId}][phone_number]`, phoneInput.value);

    Array.from(teachingSubjectsInput.selectedOptions).forEach(option => {
      formData.append(`operation[save][${userId}][teaching_subjects][]`, option.value);
    });

    formData.append(`operation[save][confirm]`, userId);

    try {
      await fetch("teacher.php", { method: "POST", body: formData });
    } catch (err) {
      console.error("Failed to save the user data:", err);
    }

    if (saveBtn.isConnected) {
      requestAnimationFrame(() => saveBtn.classList.remove("visible"));
    }
  }

  emailInput.addEventListener("input", showSave);
  phoneInput.addEventListener("input", showSave);
  teachingSubjectsInput.addEventListener("change", showSave);
  saveBtn.addEventListener("click", sendData);
});
