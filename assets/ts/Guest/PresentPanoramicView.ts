import { fetchCourses } from "./CoursesRequest.js";
import type { Course } from "./CoursesRequest.js";
import type { Ref } from "./../Common/Types.js";

// COMMON NAVBAR RESET FILTER BUTTON
const resetBtn = document.createElement("button");
resetBtn.type = "button";
resetBtn.className = "present reset";
resetBtn.textContent = "Reset";

document.addEventListener("DOMContentLoaded", async () => {

  // ELEMENT REFERENCES
  const wordFilterInput = document.querySelector<HTMLInputElement>("input[name='word_filter']");
  const subjectFilterSelect = document.querySelector<HTMLSelectElement>("select[name='subject_filter']");
  const navbar = document.querySelector<HTMLDivElement>("div[class='present navbar']");
  const courseBox = document.querySelector<HTMLDivElement>(".present.course.box");
  const courseTitle = document.querySelector<HTMLHeadingElement>(".present.course.title");
  const courseSubject = document.querySelector<HTMLHeadingElement>(".present.course.subject");
  const courseDescription = document.querySelector<HTMLParagraphElement>(".present.course.description");
  const arrowLeftButton = document.querySelector<HTMLButtonElement>(".present.arrow.left");
  const arrowRightButton = document.querySelector<HTMLButtonElement>(".present.arrow.right");

  if (!wordFilterInput
    || !subjectFilterSelect
    || !navbar
    || !courseBox
    || !courseTitle
    || !courseSubject
    || !courseDescription
    || !arrowLeftButton
    || !arrowRightButton
  ) return;

  // CURRENT PANORAMIC VIEW SLIDE INDEX
  const currentIndexRef: Ref<number> = { value: 0 };

  // RENDERING FUNCTIONS
  const emptyMessage = () => {
    courseBox.classList.add("fade-out");
    setTimeout(() => {
      courseTitle.textContent = "No courses found";
      courseSubject.textContent = "";
      courseDescription.textContent = "";
      courseBox.classList.remove("fade-out");
    }, 300);
  };

  try {
    // FETCH COURSES & INITIAL SETUP
    const courses = await fetchCourses();
    const filteredCoursesRef: Ref<Course[]> = { value: courses };

    // RENDERING LOGIC
    const showCourse = (courses: Course[], index: number) => {
      if (!courses.length) return;
      const course = courses[index];
      courseBox.classList.add("fade-out");

      setTimeout(() => {
        courseTitle.textContent = course.name;
        courseSubject.textContent = course.subject;
        courseDescription.textContent = course.description;
        courseBox.classList.remove("fade-out");
      }, 500);
    }

    // FILTERING LOGIC
    const applyFilters = () => {
      currentIndexRef.value = 0;
      const word = wordFilterInput.value.trim().toLowerCase();
      const subject = subjectFilterSelect.value;

      let filtered = courses;

      if (word !== "" && word.length >= 3) {
        if (!navbar?.contains(resetBtn)) {
          navbar?.appendChild(resetBtn);
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

      filteredCoursesRef.value = filtered;

      if (filtered.length > 0)
        showCourse(filtered, currentIndexRef.value);
      else
        emptyMessage();
    }

    // INITIAL RENDER
    if (courses && courses.length > 0)
      applyFilters();
    else
      emptyMessage();

    // RESET BUTTON LOGIC
    resetBtn.addEventListener("click", () => {
      wordFilterInput.value = "";
      subjectFilterSelect.value = "";
      requestAnimationFrame(() => resetBtn.classList.remove("visible"));
      setTimeout(() => navbar?.removeChild(resetBtn), 400);
      applyFilters();
    });

    // ARROWS SETUP
    const setupArrows = (currentIndexRef: Ref<number>, filteredCoursesRef: Ref<Course[]>) => {
      arrowLeftButton.addEventListener("click", () => {
        const courses = filteredCoursesRef.value;
        if (!courses.length) return;
        currentIndexRef.value = (currentIndexRef.value - 1 + courses.length) % courses.length;
        showCourse(courses, currentIndexRef.value);
      });

      arrowRightButton.addEventListener("click", () => {
        const courses = filteredCoursesRef.value;
        if (!courses.length) return;
        currentIndexRef.value = (currentIndexRef.value + 1) % courses.length;
        showCourse(courses, currentIndexRef.value);
      });

      document.addEventListener("keydown", (event) => {
        if (event.key === "ArrowLeft") {
          arrowLeftButton.click();
        }
        if (event.key === "ArrowRight" || event.key === "Enter") {
          arrowRightButton.click();
        }
      });
    }
    setupArrows(currentIndexRef, filteredCoursesRef);

    // FILTER INPUTS
    let filterTimeout: number | undefined;
    wordFilterInput.addEventListener("input", () => {
      clearTimeout(filterTimeout);
      filterTimeout = window.setTimeout(applyFilters, 200);
    });
    subjectFilterSelect.addEventListener("change", applyFilters);

  } catch (err) {
    console.error("Failed to fetch courses:", err);
    emptyMessage();
  }
});
