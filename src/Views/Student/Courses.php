<?php

use App\Config\LogManager;

if (isset($this->edit_selection)) {
  echo ("<table border='1'>
    <tr>
      <th>Name</th>
      <th>Description</th>
      <th>Subscribed</th>
    </tr>");

  $current_user_id = $this->current_table["students"]["id"];
  foreach ($this->current_table["courses"] as $course) {
    $course_id = $course["id"];
    $course_name = $course["name"];
    $course_description = $course["description"];
    $checked = "";

    foreach ($this->current_table["course_students"] as $course_row) {
      if ($course_row["course_id"] == $course["id"]) {
        $checked = "checked";
        break;
      }
    }

    echo ("<tr>
      <td>$course_name</td>
      <td>$course_description</td>
      <td><input type='checkbox' name='modified_table[$course_id][subscribed][$current_user_id]' $checked></td>
    </tr>");
  }

  echo ("</table><br><button type='submit' name='operation' value='save_changes'>Save Changes</button>");
}
