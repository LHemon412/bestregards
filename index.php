<?php
/*
-> Authentication System to verify if the user is allowed to access services

-> If success: go to home/
-> If failed: go to error.html
*/

include "auth.php";

// Initialization
$servername = "localhost";
$username = "root";
$password = "";
$database = "bestregards";

// Check if user already logged in
session_start();
if (isset($_SESSION["uid"])) {
  header("Location: home");
  die();
}

if (isCookieLoggedIn()) {
  $_SESSION["uid"] = $_COOKIE["uid"];
  header("Location: home");
  die();
}

// Initialization
$uid = $_GET["uid"];
$passcode = $_GET["pc"];

// No information provided
if (!isset($uid) || !isset($passcode)) {
  header("Location: error.html");
  die();
}

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
  die("Database error: " . $conn->connect_error);
}

// Requesting data and verify
$stmt = $conn->prepare("SELECT COUNT(*) FROM `notebooks` WHERE `uid`=? AND `passcode`=?");
$stmt->bind_param("is", $uid, $passcode);
$stmt->execute();
$result = $stmt->get_result();
if ($result->fetch_assoc()["COUNT(*)"] == "1") {
  setcookie("uid", $uid, time()+86400*30, "/");
  setcookie("hash", hash("sha256", bin2hex($passcode)+time()), time()+86400*30, "/");
  $_SESSION["uid"] = $uid;
  $stmt2 = $conn->prepare("UPDATE `notebooks` SET `lastLogin`=FROM_UNIXTIME(?) WHERE `uid`=?");
  $stmt2->bind_param("ii", time(), $uid);
  $stmt2->execute();
  header("Location: home");
} else {
  header("Location: error.html");
}
?>
