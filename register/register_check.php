<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "bestregards";

header("Content-type: application/json");

$return = [
  "success" => false,
  "data" => []
];

if (isset($_GET["username"])) {
  $conn = new mysqli($servername, $username, $password, $database);
  $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username=?");
  $stmt->bind_param("s", $_GET["username"]);
  $stmt->execute();
  $return["success"] = true;
  $return["data"]["username"] = ($stmt->get_result()->fetch_assoc()["COUNT(*)"] == 1);
}

if (isset($_GET["email"])) {
  $conn = new mysqli($servername, $username, $password, $database);
  $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email=?");
  $stmt->bind_param("s", $_GET["email"]);
  $stmt->execute();
  $return["success"] = true;
  $return["data"]["email"] = ($stmt->get_result()->fetch_assoc()["COUNT(*)"] == 1);
}

die(json_encode($return));
?>
