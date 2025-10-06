import { fetchCourses, Course } from "./CoursesRequest.js";

// COMMON NAVBAR RESET FILTER BUTTON

const resetBtn = document.createElement("button");
resetBtn.type = "button";
resetBtn.className = "present reset";
resetBtn.textContent = "Reset";

document.addEventListener("DOMContentLoaded", async () => {
  const wordFilterInput = document.querySelector<HTMLInputElement>("input[name='word_filter']");
  const subjectFilterSelect = document.querySelector<HTMLSelectElement>("select[name='subject_filter']");
  const navbar = document.querySelector<HTMLDivElement>("div[class='present navbar']");
  const tableBody = document.querySelector<HTMLTableElement>("#coursesTable tbody");

  if (!wordFilterInput || !subjectFilterSelect || !navbar || !tableBody) return;

  // RENDERING FUNCTIONS
  const populateTable = (courses: Course[]) => {
    tableBody.innerHTML = "";

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

      tableBody.appendChild(tr);
    });
  };

  const emptyMessage = () => {
    tableBody.innerHTML = `
      <tr>
        <td colspan="3" style="text-align: center;">
          <h3 class="present course subject">No courses found</h3>
        </td>
      </tr>
    `;
  };

  const showResetButton = () => {
    if (!navbar.contains(resetBtn)) {
      navbar.appendChild(resetBtn);
      resetBtn.classList.add("visible");
    }
  };

  const hideResetButton = () => {
    if (navbar.contains(resetBtn)) {
      resetBtn.classList.remove("visible");
      navbar.removeChild(resetBtn);
    }
  };

  try {
    // FETCHING & FILTERING
    const courses = await fetchCourses();

    const applyFilters = () => {
      const word = wordFilterInput.value.trim().toLowerCase();
      const subject = subjectFilterSelect.value;
      let filtered = courses;

      const hasWordFilter = word.length >= 3;
      const hasSubjectFilter = subject !== "";

      if (hasWordFilter) {
        filtered = filtered.filter(c =>
          c.name.toLowerCase().includes(word) ||
          c.description.toLowerCase().includes(word)
        );
      }

      if (hasSubjectFilter) {
        filtered = filtered.filter(c => c.subject === subject);
      }

      if (hasWordFilter || hasSubjectFilter) {
        showResetButton();
      } else {
        hideResetButton();
      }

      if (hasWordFilter || hasSubjectFilter) {
        if (filtered.length > 0) {
          populateTable(filtered);
        } else {
          emptyMessage();
        }
      } else {
        populateTable(courses);
      }
    };

    if (courses.length > 0)
      populateTable(courses);
    else
      emptyMessage();

    // RESET BUTTON HANDLING
    resetBtn.addEventListener("click", () => {
      wordFilterInput.value = "";
      subjectFilterSelect.value = "";
      hideResetButton();
      populateTable(courses);
    });

    // FILTER INPUT EVENTS
    let filterTimeout: number | undefined;
    wordFilterInput.addEventListener("input", () => {
      clearTimeout(filterTimeout);
      filterTimeout = window.setTimeout(applyFilters, 200);
    });
    subjectFilterSelect.addEventListener("change", applyFilters);

  } catch (err) {
    console.error("Failed to load courses:", err);
    emptyMessage();
  }
});
