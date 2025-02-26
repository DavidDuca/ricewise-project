<?php
header('Content-Type: application/json');

define('SECURE_ACCESS', true);
include 'config.php';

session_start(); 
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not authenticated"]);
    exit;
}

$date = $_GET['date'];
$user_id = $_SESSION['user_id']; 

$sql = "SELECT * FROM rice_intake_history WHERE user_id = ? AND intake_date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $date);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

$stmt->close();
$conn->close();
?>
