<?php
session_start(); 

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

define('SECURE_ACCESS', true);
include 'config.php';

$today = date("Y-m-d");

$sql = "SELECT sugar_amount FROM rice_intake_history WHERE intake_date = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $today, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$sugarIntakes = [];
while ($row = $result->fetch_assoc()) {
    $sugarIntakes[] = $row['sugar_amount'];
}

$stmt->close();
$conn->close();

echo json_encode($sugarIntakes);
?>
