<?php
// No information provided
if (!isset($uid) || !isset($passcode)) {
  header("Location: error.html");
}

$uid = $_GET["uid"];
$passcode = $_GET["pc"];

$servername = "localhost";
$username = "root";
$password = "";
$database = "bestregards";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
  die("Database error: " . $conn->connect_error);
}

$result = $conn->query("SELECT COUNT(*) FROM `notebooks` WHERE `uid`=$uid AND `passcode`='$passcode'");
if ($result->fetch_assoc()["COUNT(*)"] == "1") {
  session_start();
  $_SESSION["uid"] = $uid;
  header("Location: home");
  echo "Login successfully";
} else {
  header("Location: error.html");
}

?>
