<?php
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
  echo "Login successfully";
} else {
  echo "Please scan your QR Code again";
}

?>
