const loginButton = document.querySelector("button[name='login']");
const nameInput = document.querySelector("input[name='name']");
const surnameInput = document.querySelector("input[name='surname']");

loginButton.addEventListener("click", () => {
  nameInput.required = true;
  surnameInput.required = true;

  setTimeout(() => {
    nameInput.required = false;
    surnameInput.required = false;
  }, 0);
});


document.addEventListener("keydown", function (event) {

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