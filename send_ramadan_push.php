<?php
// Composer ki autoload file (agar aapne google/apiclient install kiya hai)
require_once 'vendor/autoload.php';

use Google\Client;

function sendRamadanNotification($deviceToken, $title, $body) {
    // --- CONFIGURATION ---
    // 1. Apni service account file ka naam (jo GitHub Secrets se banegi)
    $serviceAccountFile = 'service-account.json'; 
    
    // 2. Aapki Firebase Project ID (aapki JSON se li gayi)
    $projectId = 'madani-prayer-times-2e17a'; 

    try {
        // --- AUTHENTICATION ---
        $client = new Client();
        $client->setAuthConfig($serviceAccountFile);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        // Access Token hasil karna
        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

        // --- MESSAGE STRUCTURE ---
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        
        $message = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ],
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'ramadan_notifications'
                    ]
                ]
            ]
        ];

        // --- SENDING REQUEST (CURL) ---
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;

    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
}

// --- TESTING ---
// Yahan user ka actual FCM token aayega (jo mobile app se milta hai)
$testToken = "YOUR_DEVICE_FCM_TOKEN_HERE"; 
$result = sendRamadanNotification($testToken, "Ramadan Mubarak!", "Sehri ka waqt khatam honay mein 10 mint baqi hain.");

echo $result;
?>
