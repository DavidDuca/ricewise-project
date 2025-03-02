<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('SECURE_ACCESS', true);
include 'config.php';

session_start();

echo "✅ scheduler.php is loaded!\n";

if (isset($_SESSION['user_id'])) {
    $tokens = fetchDeviceTokens($_SESSION['user_id']);
    if (!empty($tokens)) {
        sendFCMNotifications($tokens);
    } else {
        echo "❌ No tokens found for the logged-in user.\n";
    }
} else {
    echo "❌ User is not logged in.\n";
}

function fetchDeviceTokens($userId) {
    global $conn;
    $tokens = [];
    $query = "SELECT device_token FROM user_tokens WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['device_token'])) {
                $tokens[] = $row['device_token'];
            }
        }
    } else {
        echo "❌ Database Error: " . mysqli_error($conn) . "\n";
    }

    echo "📋 Fetched Tokens for User ID $userId: " . json_encode($tokens) . "\n";
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
                    "title" => "⚠️ Sugar Intake Warning!",
                    "body" => "You have reached 25g of sugar intake. Monitor your diet carefully!",
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