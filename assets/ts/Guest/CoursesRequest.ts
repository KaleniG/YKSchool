export interface Course {
  name: string,
  description: string,
  subject: string
}

export async function fetchCourses(): Promise<Course[]> {
  try {
    const response = await fetch("guest.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        "X-Requested-With": "XMLHttpRequest"
      },
      body: ""
    });

    if (!response.ok) {
      throw new Error(`Request failed with status ${response.status}`);
    }

    const data = await response.json();
    return data as Course[];
  } catch (err) {
    console.error("Failed to fetch teachers:", err);
    throw err;
  }
}