<?php
header("Content-Type: application/json"); // Ensure JSON response

$servername = "localhost";
$username = "root";
$password = "";
$database = "sugar_monitoring";

// Start timer
$start_time = microtime(true);

$conn = mysqli_connect($servername, $username, $password, $database);

// End timer
$end_time = microtime(true);
$response_time = ($end_time - $start_time) * 1000; // Convert to milliseconds

$status = "";
$color = "";

if (!$conn) {
    $status = "Connection failed: " . mysqli_connect_error();
    $color = "red"; // 🔴 If connection fails
} else {
    $status = round($response_time, 2) . " ms";

    // Determine connection strength and color
    if ($response_time < 50) {
        $status .= "<br>Connection Status: ✅ Strong";
        $color = "green"; // 🟢 Strong connection
    } elseif ($response_time < 150) {
        $status .= "<br>Connection Status: ⚠️ Moderate";
        $color = "orange"; // 🟠 Moderate connection
    } else {
        $status .= "<br>Connection Status: ❌ Weak";
        $color = "red"; // 🔴 Weak connection
    }
}

// Send JSON response
echo json_encode(["status" => $status, "color" => $color]);

mysqli_close($conn);
?>
