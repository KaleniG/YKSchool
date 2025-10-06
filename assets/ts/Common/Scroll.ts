document.addEventListener("DOMContentLoaded", () => {
  // SETTING SCROLL SPEED FOR TEXTAREAS AND MULTIPLE SELECTS
  function setCustomScrollSpeed<T extends HTMLElement>(elements: NodeListOf<T>, speed = 0.15) {
    elements.forEach(el => {
      el.addEventListener(
        "wheel",
        e => {
          e.preventDefault();
          el.scrollTop += e.deltaY * speed;
        },
        { passive: false }
      );
    });
  }

  setCustomScrollSpeed(document.querySelectorAll<HTMLTextAreaElement>("textarea"));
  setCustomScrollSpeed(document.querySelectorAll<HTMLSelectElement>("select[multiple]"));
});
