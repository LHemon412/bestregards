<?php
/*
-> Verify if user is logged in

-> If yes: require page.html
-> If no: go to ../error.html
*/

// Verify if user is logged in
session_start();

if (isset($_SESSION["uid"])) {
  require("page.html");
  die();
}

include "../auth.php";
if (isCookieLoggedIn()) {
  $_SESSION["uid"] = $_COOKIE["uid"];
  require("page.html");
  die();
}

header("Location: ../error.html");
?>
