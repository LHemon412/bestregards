<?php
include "../auth.php";

// Initialization
include_once("databaseSettings.php");

if (isLoggedIn()) {
  header("Location: ../home");
  die();
}

$uid = $_GET["uid"];
$pc = $_GET["pc"];

if (!isset($uid) or !isset($pc)) {
  header("Location: ../error.html");
  die();
}

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
$stmt = $conn->prepare("SELECT COUNT(*) FROM notebooks WHERE uid=? AND passcode=? AND registered=0");
$stmt->bind_param("is", $uid, $pc);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()["COUNT(*)"] == 1) {
  session_start();
  $_SESSION["uid"] = $uid;
  $_SESSION["pc"] = $pc;
  require("page.html");
} else {
  header("Location: ../error.html");
  die();
}


 ?>
