const editSelection = document.querySelector("select[name='edit_selection']");
const presentSelection = document.querySelector("select[name='view_format']");
const mainForm = document.querySelector("form");

if (editSelection) {
  editSelection.addEventListener("change", function () {
    mainForm.submit();
  })
}

if (presentSelection) {
  presentSelection.addEventListener("change", function () {
    mainForm.submit();
  })
}