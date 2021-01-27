<?php
/*
-> Verify if user is logged in

-> If yes: require page.html
-> If no: go to ../error.html
*/

// Verify if user is logged in
include "../auth.php";

if (isLoggedIn()) {
  require("page.html");
  die();
}

header("Location: ../login");
?>
