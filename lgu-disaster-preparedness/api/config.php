<?php
/**
 * API Configuration File
 * Contains database and external API configurations
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lgu_disaster_preparedness');

// Gemini API Configuration
// Get your API key from: https://ai.google.dev/
// Replace with your actual Gemini API key
define('GEMINI_API_KEY', 'AIzaSyCz-eh9uov4Sdb53gQxreO8RD0zsKccJ8Q');

// Gemini API Endpoint
// Using v1 with gemini-2.5-flash (latest and fastest model)
// Alternative: v1/models/gemini-2.5-pro or v1beta/models/gemini-1.5-pro
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent');
// // API Response Settings
define('API_TIMEOUT', 30); // seconds
define('MAX_RETRIES', 3);

// Error Reporting (set to false in production)
define('DEBUG_MODE', true);

// CORS Headers (if needed for frontend)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/**
 * Get Gemini API Key
 * Checks if API key is configured
 */
function getGeminiApiKey() {
    $apiKey = GEMINI_API_KEY;
    
    // Check if API key is set and not empty or placeholder
    // IMPORTANT: Check for placeholder, NOT the actual API key value!
    if (empty($apiKey) || $apiKey === 'YOUR_GEMINI_API_KEY_HERE') {
        return null;
    }
    
    return $apiKey;
}

/**
 * Database Connection
 */
function getDbConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        if (DEBUG_MODE) {
            error_log("Database connection error: " . $e->getMessage());
        }
        return null;
    }
}

/**
 * Send JSON Response
 */
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

/**
 * Send Error Response
 */
function sendErrorResponse($message, $statusCode = 400, $details = null) {
    $response = [
        'success' => false,
        'error' => $message
    ];
    
    if (DEBUG_MODE && $details !== null) {
        $response['details'] = $details;
    }
    
    sendJsonResponse($response, $statusCode);
}

/**
 * Send Success Response
 */
function sendSuccessResponse($data, $message = null) {
    $response = [
        'success' => true,
        'data' => $data
    ];
    
    if ($message !== null) {
        $response['message'] = $message;
    }
    
    sendJsonResponse($response);
}
