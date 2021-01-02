<?php
/*
-> Verify if user is logged in

-> If yes: require page.html
-> If no: go to ../error.html
*/

// Verify if user is logged in
session_start();
if (!isset($_SESSION["uid"])) {
  header("Location: ../error.html");
}

require("page.html");
?>
