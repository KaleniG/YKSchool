// assets/js/Guest/CourseCarousel.js
import { fetchCourses } from "./CoursesRequest.js";

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

// self-initialize
window.addEventListener("DOMContentLoaded", async () => {
  const currentIndexRef = { value: 0 };

  try {
    const courses = await fetchCourses();

    if (courses && courses.length > 0) {
      showCourse(courses, currentIndexRef.value);
    } else {
      emptyMessage();
    }

    setupArrows(courses, currentIndexRef);
  } catch (err) {
    console.error("Failed to fetch courses:", err);
    emptyMessage();
  }
});
