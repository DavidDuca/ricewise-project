<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('SECURE_ACCESS', true);
include 'config.php';

date_default_timezone_set('Asia/Manila');

echo "✅ scheduler.php is running...\n";

$lastSentTime = "";

while (true) {
    $now = date('H:i');
    $mealTimes = ["08:00", "13:00", "17:40"];
    echo "⏳ Checking meal times... Current Time: $now\n";
    if (in_array($now, $mealTimes) && $lastSentTime !== $now) {
        echo "✅ Time Matched! Sending notification...\n";
        $tokens = fetchDeviceTokens();
        if (!empty($tokens)) {
            sendFCMNotifications($tokens);
        } else {
            echo "❌ No tokens found.\n";
        }
        $lastSentTime = $now;
    } else {
        echo "❌ No match found or already sent.\n";
    }

    sleep(10);
}

function fetchDeviceTokens() {
    global $conn;
    $tokens = [];

    $query = "SELECT device_token FROM user_tokens";
    $result = mysqli_query($conn, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['device_token'])) {
                $tokens[] = $row['device_token'];
            }
        }
    } else {
        echo "❌ Database Error: " . mysqli_error($conn) . "\n";
    }
    echo "📋 Fetched Tokens: " . json_encode($tokens) . "\n";
    return $tokens;
}

function sendFCMNotifications($tokens) {
    require 'vendor/autoload.php';
    $credential = new Google\Auth\Credentials\ServiceAccountCredentials(
        "https://www.googleapis.com/auth/firebase.messaging",
        json_decode(file_get_contents("pvKey.json"), true)
    );
    $token = $credential->fetchAuthToken(Google\Auth\HttpHandler\HttpHandlerFactory::build());
    $ch = curl_init("https://fcm.googleapis.com/v1/projects/mywebapp-ed956/messages:send");
    foreach ($tokens as $deviceToken) {
        $payload = json_encode([
            "message" => [
                "token" => $deviceToken,
                "notification" => [
                    "title" => "🍚 Time to Log Your Meal!",
                    "body" => "Don't forget to track your meal intake. Stay consistent and keep your records updated! ✅",
                    "image" => "https://w7.pngwing.com/pngs/227/16/png-transparent-steamed-rice-in-a-bowl-cooked-rice-glutinous-rice-bowl-sticky-rice-recipe-rice-cooker-rice-bags-thumbnail.png"
                ],
                "webpush" => [
                    "fcm_options" => [
                        "link" => "https://ricewise.42web.io"
                    ]
                ]
            ]
        ]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token['access_token']
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo "❌ cURL Error for token ($deviceToken): " . curl_error($ch) . "\n";
        } else {
            echo "📩 Notification sent to $deviceToken: " . $response . "\n";
        }
    }
    curl_close($ch);
}
?>
