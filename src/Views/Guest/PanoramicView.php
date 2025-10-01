<div class="present panoramic">
  <button type="button" class="present arrow left">◀</button>

  <div class="present course box">
    <h2 class="present course title"></h2>
    <h3 class="present course subject"></h3>
    <p class="present course description"></p>
  </div>

  <button type="button" class="present arrow right">▶</button>
</div>

<script>
  let courses = [];
  let currentIndex = 0;

  function fetchCourses() {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "guest.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        try {
          courses = JSON.parse(xhr.responseText);
          console.log("Got courses:", courses);
          showCourse(currentIndex);
        } catch (e) {
          console.error("Invalid JSON:", xhr.responseText);
        }
      }
    };

    xhr.send();
  }

  function showCourse(index) {
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


  document.querySelector(".present.arrow.left").addEventListener("click", () => {
    if (!courses.length) return;
    currentIndex = (currentIndex - 1 + courses.length) % courses.length;
    showCourse(currentIndex);
  });

  document.querySelector(".present.arrow.right").addEventListener("click", () => {
    if (!courses.length) return;
    currentIndex = (currentIndex + 1) % courses.length;
    showCourse(currentIndex);
  });

  window.addEventListener("DOMContentLoaded", fetchCourses);
</script>