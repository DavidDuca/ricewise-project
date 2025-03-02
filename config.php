<?php
if (!defined('SECURE_ACCESS')) {
    die("Direct access not allowed.");
}

$servername = "localhost"; 
$username = "root";
$password = "";
$dbname = "sugar_monitoring";

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8");
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
