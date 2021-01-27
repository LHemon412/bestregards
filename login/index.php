<?php
include "../auth.php";

if (isLoggedIn()) {
  header("Location: ../home");
  die();
}

require("page.html");
 ?>
