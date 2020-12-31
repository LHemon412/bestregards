<?php
/*
-> Authentication System to verify if the user is allowed to access services

-> If success: go to home/
-> If failed: go to error.html
*/


// No information provided
if (!isset($uid) || !isset($passcode)) {
  header("Location: error.html");
}

// Check if user already logged in
session_start();
if (isset($_SESSION["uid"])) {
  header("Location: home");
}

// Initialization
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

// Requesting data and verify
$stmt = $conn->prepare("SELECT COUNT(*) FROM `notebooks` WHERE `uid`=? AND `passcode`=?");
$stmt->bind_param("ss", $uid, $passcode);
$stmt->execute();
$result = $stmt->get_result();
if ($result->fetch_assoc()["COUNT(*)"] == "1") {
  $_SESSION["uid"] = $uid;
  header("Location: home");
} else {
  header("Location: error.html");
}
?>
