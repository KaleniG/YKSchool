<?php

use App\Config\Path;
use App\Config\AssetManager;

$asset = new AssetManager();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?= $asset->importCSS(); ?>
  <title>Guest</title>
</head>

<body class="present">
  <form method="post" class="present">
    <div class="present navbar">
      <button type="submit" name="page" value="Home.php" class="present">Back</button>
      <select name="view_format" class="present navbar">
        <option value="table" <?= ($this->view_format == "table") ? "selected" : "" ?>>Table View</option>
        <option value="panoramic" <?= ($this->view_format == "panoramic") ? "selected" : "" ?>>Panoramic View</option>
      </select>
      <input type="text" name="word_filter" value="<?= $this->word_filter ?>" minlength="3" autocomplete="on" autocorrect="off" autocapitalize="off" spellcheck="false" class=" present navbar">
      <select name="subject_filter" class="present navbar">
        <option value="" selected>Subject</option>
        <?php foreach ($this->subjects as $subject):
          $name = $subject["name"];
          $selected = ($name == $this->subject_filter) ? "selected" : "";
        ?>
          <option value="<?= $name ?>" <?= $selected ?>><?= $name ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php
    switch ($this->view_format) {
      case "table":
        include(Path::views("Guest/TableView.php"));
        break;
      case "panoramic":
        include(Path::views("Guest/PanoramicView.php"));
        break;
    }
    ?>
  </form>
</body>

</html>

<!-- SCRIPTS LOADING -->
<script src="assets/js/Common/ReloadSelection.js"></script>