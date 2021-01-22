<?php
include "../auth.php";
header("Content-type: application/json");
if (isLoggedIn()) {
  session_start();
  echo json_encode(["username" => $_SESSION["username"]]);
} else {
  header("Location: login");
}
?>
