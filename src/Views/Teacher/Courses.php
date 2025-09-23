<?php

use App\Config\LogManager;

if (isset($this->edit_selection)) {
  echo ("<table border='1'>
    <tr>
      <th>Name</th>
      <th>Description</th>
    </tr>");

  
  foreach ($this->current_table["course_teachers"] as $course) {
    $course_id = $course["id"];
    $course_name = $course["name"];
    $course_description = $course["description"];

    echo ("<tr>
      <th>$course_name</th>
      <th><textarea name='modified_table[$course_id][description]'>$course_description</textarea></th>
    </tr>");
  }

  echo ("</table><br><button type='submit' name='operation' value='save_changes'>Save Changes</button>");
}
