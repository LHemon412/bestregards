<?php
include_once("databaseSettings.php");

function isLoggedIn() {
  return isSessionLoggedIn() or isCookieLoggedIn();
}

function isSessionLoggedIn() {
  if (!isset($_SESSION)) {
    session_start();
  }
  return isset($_SESSION["username"]);
}

function isCookieLoggedIn() {
  return false;
  if (isset($_COOKIE["uid"]) and isset($_COOKIE["hash"])) {
    $uid = $_COOKIE["uid"];
    $hash = $_COOKIE["hash"];
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
    $stmt = $conn->prepare("SELECT SHA2(HEX(passcode)+UNIX_TIMESTAMP(lastLogin), 256) AS hash FROM `notebooks` WHERE `uid`=?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->fetch_assoc()["hash"] == $hash) {
      return true;
    } else {
      return false;
    }
  } else {
    return false;
  }
}

function authLogin($username, $password) {
  $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
  $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username=? AND password=?");
  $stmt->bind_param("ss", $username, $password);
  $stmt->execute();
  if ($stmt->get_result()->fetch_assoc()["COUNT(*)"] == 1) {
    return true;
  }
  $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email=? AND password=?");
  $stmt->bind_param("ss", $username, $password);
  $stmt->execute();
  if ($stmt->get_result()->fetch_assoc()["COUNT(*)"] == 1) {
    return true;
  }
  return false;
}

?>
