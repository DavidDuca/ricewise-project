<?php
define('SECURE_ACCESS', true);
include 'config.php'; 

$user_id = $_SESSION['user_id'];
$date = date("Y-m-d"); 

$sql = "SELECT SUM(sugar_amount) as totalSugar FROM sugar_intake WHERE user_id = ? AND date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $date);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$totalSugar = $row['totalSugar'] ?? 0;
echo json_encode(["totalSugar" => $totalSugar]);
?>
