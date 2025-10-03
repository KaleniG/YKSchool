import { fetchCourses } from "./CoursesRequest.js";

const wordFilterInput = document.querySelector("input[name='word_filter']");
const subjectFilterSelect = document.querySelector("select[name='subject_filter']");
const navbar = document.querySelector("div[class='present navbar']");

const resetBtn = document.createElement("button");
resetBtn.type = "button";
resetBtn.className = "present reset";
resetBtn.textContent = "Reset";

function showCourse(courses, index) {
  if (!courses.length) return;

  const courseBox = document.querySelector(".present.course.box");
  const course = courses[index];

  courseBox.classList.add("fade-out");

  setTimeout(() => {
    courseBox.querySelector(".present.course.title").textContent = course.name;
    courseBox.querySelector(".present.course.subject").textContent = course.subject;
    courseBox.querySelector(".present.course.description").textContent = course.description;

    courseBox.classList.remove("fade-out");
  }, 500);
}

function setupArrows(courses, currentIndexRef) {
  document.querySelector(".present.arrow.left").addEventListener("click", () => {
    if (!courses.length) return;
    currentIndexRef.value = (currentIndexRef.value - 1 + courses.length) % courses.length;
    showCourse(courses, currentIndexRef.value);
  });

  document.querySelector(".present.arrow.right").addEventListener("click", () => {
    if (!courses.length) return;
    currentIndexRef.value = (currentIndexRef.value + 1) % courses.length;
    showCourse(courses, currentIndexRef.value);
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "ArrowLeft") {
      document.querySelector(".present.arrow.left").click();
    }
    if (event.key === "ArrowRight" || event.key === "Enter") {
      document.querySelector(".present.arrow.right").click();
    }
  });
}

function emptyMessage() {
  const courseBox = document.querySelector(".present.course.box");
  courseBox.querySelector(".present.course.title").textContent = "No courses found";
  courseBox.querySelector(".present.course.subject").textContent = "";
  courseBox.querySelector(".present.course.description").textContent = "";
}

window.addEventListener("DOMContentLoaded", async () => {
  const currentIndexRef = { value: 0 };

  try {
    const courses = await fetchCourses();
    const filteredCoursesRef = { value: courses };

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
      currentIndexRef.value = 0;
      const word = wordFilterInput.value.trim().toLowerCase();
      const subject = subjectFilterSelect.value;

      let filtered = courses;

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

      filteredCoursesRef.value = filtered; // update the filtered array

      if (filtered.length > 0) {
        showCourse(filtered, currentIndexRef.value);
      } else {
        emptyMessage();
      }
    }

    function setupArrows(currentIndexRef, filteredCoursesRef) {
      document.querySelector(".present.arrow.left").addEventListener("click", () => {
        const courses = filteredCoursesRef.value;
        if (!courses.length) return;
        currentIndexRef.value = (currentIndexRef.value - 1 + courses.length) % courses.length;
        showCourse(courses, currentIndexRef.value);
      });

      document.querySelector(".present.arrow.right").addEventListener("click", () => {
        const courses = filteredCoursesRef.value;
        if (!courses.length) return;
        currentIndexRef.value = (currentIndexRef.value + 1) % courses.length;
        showCourse(courses, currentIndexRef.value);
      });

      document.addEventListener("keydown", function (event) {
        if (event.key === "ArrowLeft") {
          document.querySelector(".present.arrow.left").click();
        }
        if (event.key === "ArrowRight" || event.key === "Enter") {
          document.querySelector(".present.arrow.right").click();
        }
      });
    }

    wordFilterInput.addEventListener("input", applyFilters);
    subjectFilterSelect.addEventListener("change", applyFilters);

    setupArrows(currentIndexRef, filteredCoursesRef);
  } catch (err) {
    console.error("Failed to fetch courses:", err);
    emptyMessage();
  }
});
