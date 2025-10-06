document.addEventListener("DOMContentLoaded", () => {
  // HANDLING FORM SUBMIT ON SELECT OPTION CHANGE
  const editSelection = document.querySelector<HTMLSelectElement>("select[name='edit_selection']");
  const presentSelection = document.querySelector<HTMLSelectElement>("select[name='view_format']");
  const mainForm = document.querySelector<HTMLFormElement>("form");

  editSelection?.addEventListener("change", () => {
    mainForm?.submit();
  })

  presentSelection?.addEventListener("change", () => {
    mainForm?.submit();
  })
});