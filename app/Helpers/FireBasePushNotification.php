<?php
namespace App\Helpers;


use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;

class FireBasePushNotification
{

    public function toDevice($device, $body, $title = "My favorite App")
    {
        $data = [
            'token' => $device,
            'title' => $title,
            'body' => $body
        ];

        return $this->send($data);
    }

    public function send($data)
    {
        
        $fcm = $data['token'];
        $title = $data['title'];
        $description = $data['body'];

        $projectId = "swooposhospitalitypos";//config('services.fcm.project_id'); # INSERT COPIED PROJECT ID

        // $credentialsFilePath = Storage::path('app/json/swooposhospitalitypos.json');
        $credentialsFilePath = storage_path("app/json/swooposhospitalitypos.json");
        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        $access_token = $token['access_token'];

        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];

        $data = [
            "message" => [
                "token" => $fcm,
                "notification" => [
                    "title" => $title,
                    "body" => $description,
                ],
            ]
        ];
        $payload = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        

        if ($err) {
            // return $err;
            return response()->json([
                'message' => 'Curl Error: ' . $err
            ], 500);
        } else {
            // return $response;
            return response()->json([
                'message' => 'Notification has been sent',
                'response' => json_decode($response, true)
            ]);
        }

    }
}