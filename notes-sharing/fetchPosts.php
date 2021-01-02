<?php
/*
-> Fetch the list of post
*/
$servername = "localhost";
$username = "root";
$password = "";
$database = "bestregards";


$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
  die("Database error: " . $conn->connect_error);
}

if (isset($_GET["omit"])) {
  $lower_limit = $_GET["omit"];
  $upper_limit = intval($_GET["omit"]) + 10;
  $stmt = $conn->prepare("SELECT * FROM `studynotes` ORDER BY timestamp DESC LIMIT ?, ?");
  $stmt->bind_param("ii", $lower_limit, $upper_limit);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $result = $conn->query("SELECT * FROM `studynotes` ORDER BY timestamp DESC LIMIT 10");
}
echo json_encode($result->fetch_all());
?>
