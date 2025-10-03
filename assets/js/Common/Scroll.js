const multipleSelects = document.querySelectorAll("select[multiple]");
const textareas = document.querySelectorAll("textarea");

if (textareas) {
  textareas.forEach(textarea => {
    textarea.addEventListener("wheel", (e) => {
      e.preventDefault();
      textarea.scrollTop += e.deltaY * 0.01;
    }, {
      passive: false
    });
  });
}

if (multipleSelects) {
  multipleSelects.forEach(select => {
    select.addEventListener("wheel", (e) => {
      e.preventDefault();
      select.scrollTop += e.deltaY * 0.01;
    }, {
      passive: false
    });
  });
}
