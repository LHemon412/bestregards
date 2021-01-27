<?php

// Error code explanation
// 0: Insufficient post data
// 1: Non-image file uploaded as image format
// 2: File size too large (Currently 5MB - 5,000,000B)
// 3: File IO Error

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

if (!isset($_POST["caption"])) {
  echo json_encode(["error" => ["code" => 0]]);
  die();
}

if (!isset($_POST["tags"])) {
  echo json_encode(["error" => ["code" => 0]]);
  die();
}

if (!isset($_FILES["atmts"])) {
  echo json_encode(["error" => ["code" => 0]]);
  die();
}

$return = [
  "error" => []
];

if (isset($_FILES["atmts"])) {
  $valid_files = [];
  $return["success"] = true;
  $return["received_files"] = $_FILES["atmts"];
  foreach($_FILES["atmts"]["error"] as $index => $error_code) {
    if ($error_code == "0") {
      array_push($valid_files, $index);
    }
  }
  $return["valid_files"] = $valid_files;
  $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
  $username = $_SESSION["username"];

  $stmt = $conn->prepare("SELECT COUNT(*) FROM studynotes WHERE username=?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $post_id = 1;
  if ($stmt->get_result()->fetch_assoc()["COUNT(*)"] > 0) {
    $stmt = $conn->prepare("SELECT post_id FROM studynotes WHERE username=? ORDER BY post_id DESC LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $post_id = $stmt->get_result()->fetch_assoc()["post_id"] + 1;
  }

  $target_dir = "data/{$username}/{$post_id}/";
  $image_extensions = ["jpg", "jpeg", "png"];
  foreach($valid_files as $i) {
    $file_name = $_FILES["atmts"]["name"][$i];
    $checkOk = true;

    // Check for error code 1
    if (in_array(pathinfo($file_name, PATHINFO_EXTENSION), $image_extensions)) {
      if (!getimagesize($_FILES["atmts"]["tmp_name"][$i])) {
        array_push($return["error"], [
          "file" => $file_name,
          "code" => 1
        ]);
        $checkOk = false;
      }
    }

    // Check for error code 2
    if ($_FILES["atmts"]["size"][$i] > 5000000) {
      array_push($return["error"], [
        "file" => $file_name,
        "code" => 2
      ]);
      $checkOk = false;
    }
  }

  // Die if any error occured
  if (count($return["error"])) {
    $return["success"] = false;
    die(json_encode($return));
  }

  // Move uploaded files to designated locations
  mkdir($target_dir, 0777, true);
  $moved_files = [];
  foreach($valid_files as $i) {
    $target_file = $target_dir . basename($_FILES["atmts"]["name"][$i]);
    array_push($moved_files, $_FILES["atmts"]["name"][$i]);
    if (!move_uploaded_file($_FILES["atmts"]["tmp_name"][$i], $target_file)) {
      array_push($return["error"], [
        "file" => $file_name,
        "code" => 3
      ]);
      rmdir($target_dir);
    }
  }
  $fp = fopen($target_dir . "desc.json", "w");
  fwrite($fp, json_encode([
    "caption" => $_POST["caption"],
    "attachments" => $moved_files,
    "tags" => $_POST["tags"]
  ]));
  fclose($fp);

  // Update SQL Database
  $stmt2 = $conn->prepare("INSERT INTO `studynotes` (`username`, `timestamp`, `post_id`) VALUES (?, FROM_UNIXTIME(?), ?)");
  $timestamp = time();
  $stmt2->bind_param("sii", $username, $timestamp, $post_id);
  $stmt2->execute();
}


sleep(1);
die(json_encode($return));
?>
