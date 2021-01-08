<?php
include "../auth.php";

// Initialization
$servername = "localhost";
$username = "root";
$password = "";
$database = "bestregards";

if (isLoggedIn()) {
  header("Location: ../home");
  die();
}

require("page.html");
 ?>
