<?php
include "../auth.php";
header("Content-type: application/json");
if (!isLoggedIn()) {
  header("Location: ../login");
  die();
}

if (!isset($_POST["username"])) {
  echo json_encode(["error" => ["code" => 0]]);
  die();
}

if (!isset($_POST["mode"])) {
  echo json_encode(["error" => ["code" => 0]]);
  die();
}

include_once("../databaseSettings.php");
$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
$return = [];
if (in_array("active", $_POST["mode"])) {
  $stmt = $conn->prepare("SELECT UNIX_TIMESTAMP(startTime) AS startTime FROM timer WHERE username=? AND endTime IS NULL");
  $stmt->bind_param("s", $_POST["username"]);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows == 0) {
    $return["active"] = false;
  } else {
    $return["active"] = true;
    $return["startTime"] = $result->fetch_assoc()["startTime"];
  }
}
if (in_array("totalTime", $_POST["mode"])) {
  $stmt = $conn->prepare("SELECT SUM(UNIX_TIMESTAMP(endTime)-UNIX_TIMESTAMP(startTime)) AS totalTime FROM `timer` WHERE username=? AND endTime IS NOT NULL");
  $stmt->bind_param("s", $_POST["username"]);
  $stmt->execute();
  $totalTime = $stmt->get_result()->fetch_assoc()["totalTime"];
  if ($totalTime == null) {
    $return["totalTime"] = 0;
  } else {
    $return["totalTime"] = $totalTime;
  }
}




die(json_encode($return));
?>
