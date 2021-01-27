<?php
include "../auth.php";
header("Content-type: application/json");
if (!isLoggedIn()) {
  header("Location: ../login");
  die();
}

if (!isset($_POST["mode"])) {
  echo json_encode(["error" => ["code" => 0]]);
  die();
}

include_once("../databaseSettings.php");
$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
$now = time();
if ($_POST["mode"] == "start") {
  $stmt = $conn->prepare("INSERT INTO timer (username, startTime) VALUES (?, FROM_UNIXTIME(?))");
  $stmt->bind_param("si", $_SESSION["username"], $now);
  $stmt->execute();
  die(json_encode(["success" => true]));
} else if ($_POST["mode"] == "end") {
  $stmt = $conn->prepare("UPDATE timer SET endTime=FROM_UNIXTIME(?) WHERE username=? AND endTime IS NULL");
  $stmt->bind_param("is", $now, $_SESSION["username"]);
  $stmt->execute();
  die(json_encode(["success" => true]));
} else {
  die(json_encode(["success" => false]));
}
?>
