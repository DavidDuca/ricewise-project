<?php
define('SECURE_ACCESS', true);
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id']; 
    $meal_time = $_POST['meal_time'];
    $rice_cups = $_POST['rice_cups'];
    $rice_type = $_POST['rice_type'];
    $sugar_amount = $_POST['sugar_amount'];
    $intake_date = date('Y-m-d'); 

    $valid_meal_times = ['breakfast', 'lunch', 'dinner'];
    if (!in_array($meal_time, $valid_meal_times)) {
        die("Invalid meal time selected.");
    }

    $sql = "INSERT INTO rice_intake_history (user_id, meal_time, rice_cups, rice_type, sugar_amount, intake_date) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssds", $user_id, $meal_time, $rice_cups, $rice_type, $sugar_amount, $intake_date);

    if ($stmt->execute()) {
        echo "<script>alert('Intake history saved successfully!');
        window.location.href = 'user_dashboard.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
