<table class="present" id="coursesTable">
  <thead>
    <tr>
      <th>Name</th>
      <th>Description</th>
      <th>Subject</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>

<script type="module">
  import {
    fetchCourses
  } from './assets/js/Guest/CoursesRequest.js';

  function populateTable(courses) {
    const tbody = document.querySelector('#coursesTable tbody');
    tbody.innerHTML = '';

    courses.forEach(course => {
      const tr = document.createElement('tr');

      const tdName = document.createElement('td');
      const inputName = document.createElement('input');
      inputName.type = 'text';
      inputName.value = course.name;
      inputName.disabled = true;
      inputName.classList.add('present');
      tdName.appendChild(inputName);
      tr.appendChild(tdName);

      const tdDesc = document.createElement('td');
      const textareaDesc = document.createElement('textarea');
      textareaDesc.textContent = course.description;
      textareaDesc.disabled = true;
      textareaDesc.classList.add('present');
      textareaDesc.spell
      tdDesc.appendChild(textareaDesc);
      tr.appendChild(tdDesc);

      const tdSubject = document.createElement('td');
      const inputSubject = document.createElement('input');
      inputSubject.type = 'text';
      inputSubject.value = course.subject;
      inputSubject.disabled = true;
      inputSubject.classList.add('present');
      tdSubject.appendChild(inputSubject);
      tr.appendChild(tdSubject);

      tbody.appendChild(tr);
    });
  }

  window.addEventListener('DOMContentLoaded', async () => {
    try {
      const courses = await fetchCourses();
      populateTable(courses);
    } catch (err) {
      console.error('Failed to load courses:', err);
    }
  });
</script>