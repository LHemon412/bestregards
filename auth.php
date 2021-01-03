<?php
define("SERVERNAME", "localhost");
define("USERNAME", "root");
define("PASSWORD", "");
define("DATABASE", "bestregards");
/*$servername = "localhost";
$username = "root";
$password = "";
$database = "bestregards";*/

function isCookieLoggedIn() {
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

?>
