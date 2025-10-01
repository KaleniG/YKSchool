export function fetchCourses() {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "guest.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4) {
        if (xhr.status === 200) {
          try {
            const data = JSON.parse(xhr.responseText);
            resolve(data);
          } catch (e) {
            reject(new Error("Invalid JSON: " + xhr.responseText));
          }
        } else {
          reject(new Error("Request failed with status " + xhr.status));
        }
      }
    };

    xhr.send();
  });
}
