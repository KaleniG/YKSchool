document.addEventListener("DOMContentLoaded", () => {
  const container = document.querySelector(".edit.account");
  if (!container) return;

  const userId = container.dataset.userId;
  const emailInput = container.querySelector("input[name='email']");
  const phoneInput = container.querySelector("input[name='phone_number']");
  const teachingSubjectsInput = container.querySelector("select[name='teaching_subjects[]']");

  const saveBtn = document.createElement("button");
  saveBtn.type = "button";
  saveBtn.className = "edit account option-button save";
  saveBtn.textContent = "Save Changes";
  container.insertAdjacentElement("afterend", saveBtn);

  function showSave() {
    requestAnimationFrame(() => saveBtn.classList.add("visible"));
  }

  async function sendData() {
    const formData = new FormData();
    formData.append(`operation[save][${userId}][email]`, emailInput.value);
    formData.append(`operation[save][${userId}][phone_number]`, phoneInput.value);

    for (const option of teachingSubjectsInput.selectedOptions) {
      formData.append(`operation[save][${userId}][teaching_subjects][]`, option.value);
    }

    formData.append(`operation[save][confirm]`, userId);

    try {
      await fetch("teacher.php", { method: "POST", body: formData });
    } catch (err) {
      console.error("Failed to save the user data:", err);
    }

    requestAnimationFrame(() => saveBtn.classList.remove("visible"));
  }

  emailInput.addEventListener("input", showSave);
  phoneInput.addEventListener("input", showSave);
  teachingSubjectsInput.addEventListener("change", showSave);
  saveBtn.addEventListener("click", sendData);
});
