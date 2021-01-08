<?php
include "../auth.php";

// Error code
// 0: No
// 1: Required field not received
// 2: Session data not received
// 3: UID and passcode incorrect
// 4: UID already registered

// Initialization
$servername = "localhost";
$username = "root";
$password = "";
$database = "bestregards";

if (isLoggedIn()) {
  header("Location: ../home");
  die();
}

header("Content-type: application/json");
$return = [
  "success" => true
];

if (!isset($_POST["username"]) or !isset($_POST["password"]) or !isset($_POST["email"])) {
  $return["success"] = false;
  $return["error"] = 1;
  die(json_encode($return));
}

session_start();
$uid = $_SESSION["uid"];
$pc = $_SESSION["pc"];

if (!isset($uid) or !isset($pc)) {
  $return["success"] = false;
  $return["error"] = 2;
  die(json_encode($return));
}

$conn = new mysqli($servername, $username, $password, $database);
$stmt = $conn->prepare("SELECT registered FROM notebooks WHERE uid=? AND passcode=?");
$stmt->bind_param("is", $uid, $pc);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
  $return["success"] = false;
  $return["error"] = 3;
  die(json_encode($return));
}

if ($result->fetch_assoc()["registered"] == 1) {
  $return["success"] = false;
  $return["error"] = 4;
}

$stmt = $conn->prepare("UPDATE notebooks SET registered=1 WHERE uid=? AND passcode=?");
$stmt->bind_param("is", $uid, $pc);
$stmt->execute();
$stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $_POST["username"], $_POST["password"], $_POST["email"]);
$stmt->execute();

unset($_SESSION["uid"]);
unset($_SESSION["pc"]);
$_SESSION["username"] = $_POST["username"];

die(json_encode($return));
?>
