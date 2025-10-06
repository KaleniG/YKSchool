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
  <?= $asset->importJS(); ?>
  <title><?= $user_type ?></title>
</head>

<body class="edit">
  <form method="post" class="edit">
    <div class="edit navbar">
      <button type="submit" name="page" value="Home.php" class="edit">Logout</button>
      <select name="edit_selection" class="edit navbar">
        <option value="" disabled selected>Select an option</option>
        <?php foreach ($edit_options as $option_name => $option): ?>
          <option value="<?= $option_name ?>" <?= ($this->edit_selection == $option_name) ? "selected" : ""; ?>><?= $option["label"] ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php
    foreach ($edit_options as $option_name => $option) {
      if ($this->edit_selection == $option_name) {
        include(Path::views($option["dir"]));
        break;
      }
    }
    ?>
  </form>
</body>

</html>

<!-- SCRIPTS LOADING -->
<script src="assets/js/Common/ReloadSelection.js"></script>