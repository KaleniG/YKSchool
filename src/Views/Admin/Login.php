<?php

use App\Config\AssetManager;

$asset = new AssetManager();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?= $asset->importCSS(); ?>
  <title>Administrator</title>
</head>

<body class="login">
  <form method="post" class="login">
    <h2 class="login">Administrator</h2>
    <h3 class="login">Login</h3>
    <button type="submit" name="page" value="Home.php" class="login">Home</button>
    <input type="text" name="name" placeholder="Name" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="login">
    <input type="text" name="surname" placeholder="Surname" autocomplete="off" autocorrect="off" autocapitalize="on" spellcheck="false" class="login">
    <button type="submit" name="login" class="login">Login</button>
  </form>
  <script>
    const loginButton = document.querySelector("button[name='login']");
    const nameInput = document.querySelector("input[name='name']");
    const surnameInput = document.querySelector("input[name='surname']");

    loginButton.addEventListener("click", () => {
      nameInput.required = true;
      surnameInput.required = true;

      setTimeout(() => {
        nameInput.required = false;
        surnameInput.required = false;
      }, 0);
    });


    document.addEventListener("keydown", function(event) {

      const active = document.activeElement;

      if (event.key === "ArrowDown") {
        if (active === nameInput) {
          surnameInput.focus();
          event.preventDefault();
        }
      }

      if (event.key === "ArrowUp") {
        if (active === surnameInput) {
          nameInput.focus();
          event.preventDefault();
        }
      }

      if (event.key === "Enter") {
        if (active === nameInput && nameInput.value.trim() !== "") {
          surnameInput.focus();
          event.preventDefault();
        } else if (active === surnameInput && surnameInput.value.trim() !== "") {
          loginButton.click();
          event.preventDefault();
        }
      }
    });
  </script>
</body>

</html>