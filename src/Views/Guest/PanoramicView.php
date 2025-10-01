<div class="present panoramic">
  <button type="button" class="present arrow left">◀</button>

  <div class="present course box">
    <h2 class="present course title"></h2>
    <h3 class="present course subject"></h3>
    <p class="present course description"></p>
  </div>

  <button type="button" class="present arrow right">▶</button>
</div>

<script type="module">
  import {
    fetchCourses
  } from "./assets/js/Guest/CoursesRequest.js";

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
  }

  function emptyMessage() {
    const courseBox = document.querySelector(".present.course.box");
    courseBox.querySelector(".present.course.title").textContent = "No courses found";
    courseBox.querySelector(".present.course.subject").textContent = "";
    courseBox.querySelector(".present.course.description").textContent = "";
  }

  window.addEventListener("DOMContentLoaded", async () => {
    const currentIndexRef = {
      value: 0
    };

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
</script>