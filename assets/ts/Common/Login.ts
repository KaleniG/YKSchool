document.addEventListener("DOMContentLoaded", () => {
  const loginButton = document.querySelector<HTMLButtonElement>("button[name='login']");
  const nameInput = document.querySelector<HTMLInputElement>("input[name='name']");
  const surnameInput = document.querySelector<HTMLInputElement>("input[name='surname']");

  // LOGIN LOGIC (VALIDATION)
  loginButton?.addEventListener("click", () => {
    if (nameInput) nameInput.required = true;
    if (surnameInput) surnameInput.required = true;

    setTimeout(() => {
      if (nameInput) nameInput.required = false;
      if (surnameInput) surnameInput.required = false;
    }, 0);
  });

  // LOGIN INPUT (FORE INPUT FIELD SWITCHING AND CONFIRMING)
  if (!surnameInput || !nameInput || !loginButton) return;
  document.addEventListener("keydown", (event) => {
    const active = document.activeElement;

    if (event.key === "ArrowDown") {
      if (active === nameInput) {
        surnameInput.focus();
        event.preventDefault();
      }
    }

    if (event.key === "ArrowUp") {
      if (active === surnameInput) {
        nameInput.focus();
        event.preventDefault();
      }
    }

    if (event.key === "Enter") {
      if (active === nameInput && nameInput.value.trim() !== "") {
        surnameInput.focus();
        event.preventDefault();
      } else if (active === surnameInput && surnameInput.value.trim() !== "") {
        loginButton.click();
        event.preventDefault();
      }
    }
  });
});