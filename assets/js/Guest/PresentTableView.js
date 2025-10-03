import {
  fetchCourses
} from "./CoursesRequest.js";

const wordFilterInput = document.querySelector("input[name='word_filter']");
const subjectFilterSelect = document.querySelector("select[name='subject_filter']");
const navbar = document.querySelector("div[class='present navbar']");

const resetBtn = document.createElement("button");
resetBtn.type = "button";
resetBtn.className = "present reset";
resetBtn.textContent = "Reset";

function populateTable(courses) {
  const tbody = document.querySelector("#coursesTable tbody");
  tbody.innerHTML = "";

  courses.forEach(course => {
    const tr = document.createElement("tr");

    const tdName = document.createElement("td");
    const inputName = document.createElement("input");
    inputName.type = "text";
    inputName.value = course.name;
    inputName.disabled = true;
    inputName.classList.add("present");
    tdName.appendChild(inputName);
    tr.appendChild(tdName);

    const tdDesc = document.createElement("td");
    const textareaDesc = document.createElement("textarea");
    textareaDesc.textContent = course.description;
    textareaDesc.disabled = true;
    textareaDesc.classList.add("present");
    tdDesc.appendChild(textareaDesc);
    tr.appendChild(tdDesc);

    const tdSubject = document.createElement("td");
    const inputSubject = document.createElement("input");
    inputSubject.type = "text";
    inputSubject.value = course.subject;
    inputSubject.disabled = true;
    inputSubject.classList.add("present");
    tdSubject.appendChild(inputSubject);
    tr.appendChild(tdSubject);

    tbody.appendChild(tr);
  });
}

function emptyMessage() {
  const tbody = document.querySelector("#coursesTable tbody");
  tbody.innerHTML = `
      <tr>
        <td colspan="3" style="text-align: center;">
          <h3 class="present course subject">No courses found</h3>
        </td>
      </tr>
    `;
}

window.addEventListener("DOMContentLoaded", async () => {
  try {
    const courses = await fetchCourses();
    let courses_filtered = courses;

    if (courses && courses.length > 0) {
      applyFilters();
    } else {
      emptyMessage();
    }

    resetBtn.addEventListener("click", function () {
      wordFilterInput.value = "";
      subjectFilterSelect.value = "";
      requestAnimationFrame(() => resetBtn.classList.remove("visible"));
      setTimeout(() => navbar.removeChild(resetBtn), 400);
      applyFilters();
    });

    function applyFilters() {
      const word = wordFilterInput.value.trim().toLowerCase();
      const subject = subjectFilterSelect.value;

      let filtered = courses_filtered;

      if (word !== "" && word.length >= 3) {
        if (!navbar.contains(resetBtn)) {
          navbar.appendChild(resetBtn);
          requestAnimationFrame(() => resetBtn.classList.add("visible"));
        }
        filtered = filtered.filter(c =>
          c.name.toLowerCase().includes(word) ||
          c.description.toLowerCase().includes(word)
        );
      }

      if (subject !== "") {
        if (!navbar.contains(resetBtn)) {
          navbar.appendChild(resetBtn);
          requestAnimationFrame(() => resetBtn.classList.add("visible"));
        }
        filtered = filtered.filter(c => c.subject == subject);
      }

      if (filtered.length > 0) {
        populateTable(filtered);
      } else {
        emptyMessage();
      }
    }

    wordFilterInput.addEventListener("input", applyFilters);
    subjectFilterSelect.addEventListener("change", applyFilters);

  } catch (err) {
    console.error("Failed to load courses:", err);
    emptyMessage();
  }
});
