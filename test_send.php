<?php
/* 
https://fcm.googleapis.com/v1/projects/<YOUR-PROJECT-ID>/messages:send
Content-Type: application/json
Authorization: Bearer <YOUR-ACCESS-TOKEN>
{
  "message": {
    "token": "eEz-Q2sG8nQ:APA91bHJQRT0JJ...",
    "notification": {
      "title": "Background Message Title",
      "body": "Background message body"
    },
    "webpush": {
      "fcm_options": {
        "link": "https://dummypage.com"
      }
    }
  }
}
 */

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

require 'vendor/autoload.php';

$credential = new ServiceAccountCredentials(
    "https://www.googleapis.com/auth/firebase.messaging",
    json_decode(file_get_contents("pvKey.json"), true)
);

$token = $credential->fetchAuthToken(HttpHandlerFactory::build());

$ch = curl_init("https://fcm.googleapis.com/v1/projects/mywebapp-ed956/messages:send");

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer '.$token['access_token']
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, '{
    "message": {
    "token": "cwo1PjAksMZsQlxnqIOetr:APA91bEvSItRC_IuKlfV-WYy2YD429tJqOGZpfZ2DLTVz4bLUZzxA8GaXLOaU96TLhfcc25s1XFy2c41QgltBydq56c8WZu4R0MeTXbsFB1vqmb6WJe_KJU",
    "notification": {
        "title": "🍚 Time to Log Your Meal!",
        "body": "Do not forget to track your meal intake. Stay consistent and keep your records updated! ✅",
        "image": "https://w7.pngwing.com/pngs/227/16/png-transparent-steamed-rice-in-a-bowl-cooked-rice-glutinous-rice-bowl-sticky-rice-recipe-rice-cooker-rice-bags-thumbnail.png"
    },
    "webpush": {
        "fcm_options": {
        "link": "https://ricewise.42web.io"
        }
    }
    }
}');

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "post");

$response = curl_exec($ch);

curl_close($ch);

echo $response;