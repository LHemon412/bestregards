<?php
include "../auth.php";
// Error code
// 0: No Error
// 1: Required filed not received
// 2: Wrong credentials

header("Content-type: application/json");
$return = [
  "success" => true
];
error_reporting(E_ALL ^ E_NOTICE);

$username = $_POST["username"];
$password = $_POST["password"];
if (!isset($username) || !isset($password)) {
  $return["success"] = false;
  $return["error"] = 1;
  die(json_encode($return));
}

if (authLogin($username, $password)) {
  session_start();
  $_SESSION["username"] = $_POST["username"];
  die(json_encode($return));
} else {
  $return["success"] = false;
  $return["error"] = 2;
  die(json_encode($return));
}
?>
