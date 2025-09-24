<br>
<label>Name:</label><input type="text" value="<?= $this->user->name ?>" disabled>
<br>
<label>Surname:</label><input type="text" value="<?= $this->user->surname ?>" disabled>
<br>
<?php

$id = $this->current_table["students"]["id"];
$email = $this->current_table["students"]["email"];
$phone_number = $this->current_table["students"]["phone_number"];
$tuition_status = ($this->current_table["students"]["tuition_enabled"] == "t") ? "Enabled" : "Disabled";

echo ("<label>E-mail:</label><input type='text' name='modified_table[{$id}][email]' value='{$email}'><br>");
echo ("<label>Phone Number:</label><input type='text' name='modified_table[{$id}][phone_number]' value='{$phone_number}'><br>");
echo ("<label>Tuition Status: $tuition_status</label><br>");
echo ("<br>");
?>
<br>
<button type='submit' name='operation' value='save_changes'>Save Changes</button>