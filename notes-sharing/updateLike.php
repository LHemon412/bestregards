<?php

// Error code explanation
// 0: Insufficient post data

include_once("databaseSettings.php");
session_start();
if (!isset($_SESSION["username"])) {
  header("Location: ../error.html");
  die();
}

header("Content-type: application/json");

if (empty($_POST)) {
  echo json_encode(["error" => ["code" => 0]]);
  die();
}

if (!isset($_POST["username"])) {
  echo json_encode(["error" => ["code" => 0]]);
  die();
}

if (!isset($_POST["post_id"])) {
  echo json_encode(["error" => ["code" => 0]]);
  die();
}

if (!isset($_POST["type"])) {
  echo json_encode(["error" => ["code" => 0]]);
  die();
}

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
$stmt = $conn->prepare("SELECT likes FROM studynotes WHERE username=? AND post_id=?");
$stmt->bind_param("si", $_POST["username"], $_POST["post_id"]);
$stmt->execute();
$list = json_decode($stmt->get_result()->fetch_assoc()["likes"]);
$key = array_search($_SESSION["username"], $list);
$changed = false;
if ($_POST["type"] == "like") {
  if ($key === false) {
    array_push($list, $_SESSION["username"]);
    $changed = true;
  }
} else if ($_POST["type"] == "unlike") {
  $key = array_search($_SESSION["username"], $list);
  if ($key !== false) {
    unset($list[$key]);
    $changed = true;
  }
}
if ($changed) {
  $stmt = $conn->prepare("UPDATE studynotes SET likes=? WHERE username=? AND post_id=?");
  $stmt->bind_param("ssi", json_encode($list), $_POST["username"], $_POST["post_id"]);
}
die(json_encode([
  "success" => $stmt->execute(),
  "likes" => count($list)
]));
?>
