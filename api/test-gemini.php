<?php
/**
 * Test Gemini API Connection
 * Use this file to test if your Gemini API key is working
 * Access: http://localhost/LGUCapstone/lgu-disaster-preparedness/api/test-gemini.php
 */

require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Gemini API Test</h1>";

// Check API key
$apiKey = getGeminiApiKey();
if (!$apiKey) {
    echo "<p style='color: red;'>❌ ERROR: API key not configured!</p>";
    echo "<p>Please set GEMINI_API_KEY in config.php</p>";
    exit;
}

echo "<p style='color: green;'>✅ API Key found: " . substr($apiKey, 0, 10) . "...</p>";

// Test API call - Use v1beta with gemini-pro (most stable)
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . urlencode($apiKey);

$payload = [
    'contents' => [
        [
            'parts' => [
                [
                    'text' => 'Say "Hello" in one word.'
                ]
            ]
        ]
    ]
];

echo "<h2>Testing API Call...</h2>";
echo "<p>URL: " . htmlspecialchars($url) . "</p>";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<h3>Response:</h3>";
echo "<p><strong>HTTP Code:</strong> " . $httpCode . "</p>";

if ($error) {
    echo "<p style='color: red;'><strong>cURL Error:</strong> " . htmlspecialchars($error) . "</p>";
} else {
    echo "<p style='color: green;'>✅ No cURL errors</p>";
}

if ($httpCode === 200) {
    echo "<p style='color: green;'>✅ HTTP 200 - Success!</p>";
    $data = json_decode($response, true);
    
    if ($data && isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        $text = $data['candidates'][0]['content']['parts'][0]['text'];
        echo "<p><strong>API Response:</strong> " . htmlspecialchars($text) . "</p>";
        echo "<p style='color: green; font-size: 20px;'>🎉 API is working correctly!</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Unexpected response structure</p>";
        echo "<pre>" . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT)) . "</pre>";
    }
} else {
    echo "<p style='color: red;'>❌ HTTP Error: " . $httpCode . "</p>";
    $errorData = json_decode($response, true);
    
    if ($errorData) {
        echo "<pre>" . htmlspecialchars(json_encode($errorData, JSON_PRETTY_PRINT)) . "</pre>";
        
        if (isset($errorData['error']['message'])) {
            echo "<p style='color: red;'><strong>Error Message:</strong> " . htmlspecialchars($errorData['error']['message']) . "</p>";
        }
    } else {
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}

echo "<hr>";
echo "<p><a href='../frontend/admin-scenario-design.php'>← Back to Scenario Design</a></p>";

