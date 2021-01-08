<?php
/*
-> Authentication System to verify if the user is allowed to access services

-> If success: go to home/
-> If failed: go to error.html
*/
include "auth.php";

if (isLoggedIn()) {
  header("Location: home");
} else {
  header("Location: login");
}
?>
