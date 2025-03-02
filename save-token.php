<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('SECURE_ACCESS', true);
include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['user_id']) && isset($data['device_token'])) {
    $user_id = intval($data['user_id']);
    $device_token = $data['device_token'];
    $sql = "INSERT INTO user_tokens (user_id, device_token) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE device_token = VALUES(device_token)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('is', $user_id, $device_token);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Token saved successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save token']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
}

$conn->close();
?>