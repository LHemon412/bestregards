<?php
/*
-> Fetch the list of post
*/
header("Content-type: application/json");

$servername = "localhost";
$username = "root";
$password = "";
$database = "bestregards";


$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
  die("Database error: " . $conn->connect_error);
}

if (isset($_GET["type"])) {
  if ($_GET["type"] == "data") {
    if (isset($_GET["omit"])) {
      $lower_limit = $_GET["omit"];
      $upper_limit = intval($_GET["omit"]) + 10;
      $stmt = $conn->prepare("SELECT * FROM `studynotes` ORDER BY timestamp DESC LIMIT ?, ?");
      $stmt->bind_param("ii", $lower_limit, $upper_limit);
      $stmt->execute();
      $result = $stmt->get_result();
      echo json_encode($result->fetch_all());
    }
  } else if ($_GET["type"] == "number") {
    $result = $conn->query("SELECT COUNT(*) FROM `studynotes`");
    echo json_encode(["count" => intval($result->fetch_assoc()["COUNT(*)"])]);
  } else if ($_GET["type"] == "newposts_number") {
    if (isset($_GET["uid"]) and isset($_GET["post_id"])) {
      $stmt = $conn->prepare("SELECT COUNT(*) FROM studynotes WHERE timestamp > (SELECT timestamp FROM studynotes WHERE uid=? AND post_id=?)");
      $stmt->bind_param("ii", $_GET["uid"], $_GET["post_id"]);
      $stmt->execute();
      echo json_encode(["count" => intval($stmt->get_result()->fetch_assoc()["COUNT(*)"])]);
    }
  } else if ($_GET["type"] == "newposts_data") {
    if (isset($_GET["uid"]) and isset($_GET["post_id"])) {
      $stmt = $conn->prepare("SELECT COUNT(*) FROM studynotes WHERE timestamp > (SELECT timestamp FROM studynotes WHERE uid=? AND post_id=?)");
      $stmt->bind_param("ii", $_GET["uid"], $_GET["post_id"]);
      $stmt->execute();
      $new_posts = $stmt->get_result()->fetch_assoc()["COUNT(*)"];
      $stmt = $conn->prepare("SELECT * FROM studynotes ORDER BY timestamp DESC LIMIT ?");
      $stmt->bind_param("i", $new_posts);
      $stmt->execute();
      $result = $stmt->get_result();
      echo json_encode($result->fetch_all());
    }
  }
}
?>
