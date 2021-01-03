<?php
session_start();
unset($_SESSION["uid"]);
session_destroy();
header("Location: error.html");
?>
