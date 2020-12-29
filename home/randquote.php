<?php
/*
-> Select a random quote from the database
*/
$servername = "localhost";
$username = "root";
$password = "";
$database = "bestregards";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
  die("Database error: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM `quotes` ORDER BY RAND() LIMIT 1");
echo $result->fetch_assoc()["quote"];
?>
