<?php

class deepseek {
    private $apiKey;
    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }
    public function callDeepSeek($sysprompt, $prompt, $modell = "deepseek-chat") {
        $url = "https://api.deepseek.com/chat/completions";
        $apiKey = $this->apiKey; 

        $data = [
            "model" => $modell,
            "messages" => [
                ["role" => "system", "content" => $sysprompt],
                ["role" => "user", "content" => $prompt]
            ],
            "stream" => false
        ];

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . $apiKey
            ],
            CURLOPT_POSTFIELDS => json_encode($data)
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return ["error" => curl_error($ch)];
        }

        curl_close($ch);
        return json_decode($response, true);
    }
} 