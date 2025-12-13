<?php
/**
 * Gemini API Integration - Scenario Auto-Generation
 * 
 * This endpoint generates disaster scenarios using Google's Gemini API
 * Based on user-provided parameters (disaster type, difficulty, location, etc.)
 */

require_once __DIR__ . '/config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Only POST method is allowed', 405);
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!$input) {
    sendErrorResponse('Invalid JSON input', 400);
}

// Extract parameters
$disasterType = $input['disaster_type'] ?? '';
$difficulty = $input['difficulty'] ?? 'intermediate';
$location = $input['location'] ?? 'Barangay San Agustin, Novaliches, Quezon City';
$incidentTime = $input['incident_time'] ?? 'day';
$weatherCondition = $input['weather_condition'] ?? 'sunny';
$locationType = $input['location_type'] ?? 'building';
$additionalContext = $input['additional_context'] ?? '';

// Validate required fields
if (empty($disasterType)) {
    sendErrorResponse('Disaster type is required', 400);
}

// Get API key
$apiKey = getGeminiApiKey();
if (!$apiKey) {
    sendErrorResponse('Gemini API key is not configured. Please set GEMINI_API_KEY in config.php', 500);
}

// Build the prompt for Gemini API
$prompt = buildScenarioPrompt([
    'disaster_type' => $disasterType,
    'difficulty' => $difficulty,
    'location' => $location,
    'incident_time' => $incidentTime,
    'weather_condition' => $weatherCondition,
    'location_type' => $locationType,
    'additional_context' => $additionalContext
]);

// Call Gemini API
$result = callGeminiAPI($apiKey, $prompt);

if (!$result || !isset($result['data'])) {
    $errorMsg = $result['error'] ?? 'Failed to generate scenario. Please try again.';
    sendErrorResponse($errorMsg, 500);
}

$scenarioData = $result['data'];

// Add input parameters to response so frontend can populate form fields
$scenarioData['disaster_type'] = $disasterType;
$scenarioData['difficulty'] = $difficulty;
$scenarioData['incident_time'] = $incidentTime;
$scenarioData['weather_condition'] = $weatherCondition;
$scenarioData['location_type'] = $locationType;

// Return the generated scenario
sendSuccessResponse($scenarioData, 'Scenario generated successfully');

/**
 * Build the prompt for Gemini API
 */
function buildScenarioPrompt($params) {
    $disasterType = ucfirst($params['disaster_type']);
    $difficulty = ucfirst($params['difficulty']);
    $location = $params['location'];
    $incidentTime = ucfirst($params['incident_time']);
    $weatherCondition = ucfirst($params['weather_condition']);
    $locationType = ucfirst($params['location_type']);
    
    $prompt = "You are an expert disaster preparedness trainer creating realistic training scenarios for Local Government Unit (LGU) personnel in the Philippines.

Create a detailed disaster scenario with the following specifications:

**Disaster Type:** {$disasterType}
**Difficulty Level:** {$difficulty}
**Location:** {$location}
**Time of Incident:** {$incidentTime}
**Weather Condition:** {$weatherCondition}
**Location Type:** {$locationType}

**Requirements:**
1. Create a realistic, detailed scenario description (2-3 paragraphs) that feels authentic for a Philippine LGU context
2. Include specific details about the location (use local context - streets, buildings, community characteristics)
3. Describe the initial conditions and how the disaster unfolds
4. Include realistic challenges that LGU personnel would face (resource limitations, communication issues, evacuation needs)
5. Make it appropriate for {$difficulty} level training
6. Consider the weather and time of day in the scenario

**Output Format (JSON ONLY - No markdown, no code blocks, just raw JSON):**
{
  \"title\": \"A concise, descriptive scenario title (max 60 characters)\",
  \"description\": \"A detailed 2-3 paragraph scenario description that sets the scene and describes the disaster situation\",
  \"initial_conditions\": \"Key initial conditions and context (1 paragraph)\",
  \"challenges\": [
    \"Challenge 1 that personnel must address\",
    \"Challenge 2 that personnel must address\",
    \"Challenge 3 that personnel must address\"
  ],
  \"expected_actions\": [
    \"Expected action 1 that personnel should take\",
    \"Expected action 2 that personnel should take\",
    \"Expected action 3 that personnel should take\"
  ],
  \"safety_notes\": \"Important safety warnings or considerations for this scenario\",
  \"learning_objectives\": [
    \"Learning objective 1\",
    \"Learning objective 2\",
    \"Learning objective 3\"
  ]
}

**CRITICAL: Return ONLY valid JSON. Do not include markdown code blocks, explanations, or any text outside the JSON object. Start with { and end with }.**";

    if (!empty($params['additional_context'])) {
        $prompt .= "\n\n**Additional Context:** " . $params['additional_context'];
    }
    
    return $prompt;
}

/**
 * Call Gemini API
 */
function callGeminiAPI($apiKey, $prompt) {
    $url = GEMINI_API_URL . '?key=' . urlencode($apiKey);
    
    $payload = [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => $prompt
                    ]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 2048
        ]
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, API_TIMEOUT);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        $errorMsg = "Network error: " . $error;
        if (DEBUG_MODE) {
            error_log("Gemini API cURL error: " . $error);
        }
        return ['error' => $errorMsg, 'data' => null];
    }
    
    if ($httpCode !== 200) {
        $errorData = json_decode($response, true);
        $errorMsg = "API Error (HTTP {$httpCode})";
        
        if (isset($errorData['error']['message'])) {
            $errorMsg = $errorData['error']['message'];
        } elseif (isset($errorData['error'])) {
            $errorMsg = is_string($errorData['error']) ? $errorData['error'] : "API Error: " . json_encode($errorData['error']);
        }
        
        if (DEBUG_MODE) {
            error_log("Gemini API HTTP error: " . $httpCode . " - " . $response);
        }
        return ['error' => $errorMsg, 'data' => null];
    }
    
    $data = json_decode($response, true);
    
    if (!$data) {
        $errorMsg = "Invalid JSON response from API";
        if (DEBUG_MODE) {
            error_log("Gemini API invalid JSON: " . $response);
        }
        return ['error' => $errorMsg, 'data' => null];
    }
    
    // Handle different response formats
    $generatedText = null;
    
    // Check for standard text response
    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        $generatedText = $data['candidates'][0]['content']['parts'][0]['text'];
    }
    // Check for structured JSON response (when responseMimeType is used)
    elseif (isset($data['candidates'][0]['content']['parts'][0])) {
        $part = $data['candidates'][0]['content']['parts'][0];
        // If it's already JSON, use it directly
        if (isset($part['text'])) {
            $generatedText = $part['text'];
        } elseif (is_array($part) && !isset($part['text'])) {
            // Response might be structured differently
            $generatedText = json_encode($part);
        }
    }
    
    if (!$generatedText) {
        $errorMsg = "Unexpected API response structure";
        if (isset($data['error'])) {
            $errorMsg = $data['error']['message'] ?? "API Error";
        }
        if (DEBUG_MODE) {
            error_log("Gemini API unexpected response: " . json_encode($data));
        }
        return ['error' => $errorMsg, 'data' => null];
    }
    
    // Try to extract JSON from the response
    $scenarioData = extractJsonFromResponse($generatedText);
    
    if (!$scenarioData) {
        // If JSON extraction fails, try to parse as text and create structured response
        if (DEBUG_MODE) {
            error_log("Failed to extract JSON, attempting text parsing. Response: " . substr($generatedText, 0, 500));
        }
        $scenarioData = parseTextResponse($generatedText);
    }
    
    // Validate required fields
    $scenarioData = validateScenarioData($scenarioData);
    
    return ['error' => null, 'data' => $scenarioData];
}

/**
 * Extract JSON from Gemini response
 * Handles markdown code blocks, plain JSON, and direct JSON responses
 */
function extractJsonFromResponse($text) {
    if (empty($text)) {
        return null;
    }
    
    // First, try to decode the entire text as JSON (in case it's already valid JSON)
    $data = json_decode($text, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
        return $data;
    }
    
    // Remove markdown code blocks if present
    $text = preg_replace('/```json\s*/i', '', $text);
    $text = preg_replace('/```\s*/', '', $text);
    $text = trim($text);
    
    // Try to decode again after removing markdown
    $data = json_decode($text, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
        return $data;
    }
    
    // Try to find JSON object in the response
    $jsonStart = strpos($text, '{');
    $jsonEnd = strrpos($text, '}');
    
    if ($jsonStart === false || $jsonEnd === false || $jsonEnd <= $jsonStart) {
        return null;
    }
    
    $jsonString = substr($text, $jsonStart, $jsonEnd - $jsonStart + 1);
    $data = json_decode($jsonString, true);
    
    // Validate that we got valid JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        if (DEBUG_MODE) {
            error_log("JSON decode error: " . json_last_error_msg() . " - Text: " . substr($jsonString, 0, 200));
        }
        return null;
    }
    
    return $data;
}

/**
 * Parse text response if JSON extraction fails
 */
function parseTextResponse($text) {
    // Fallback: create structured data from text
    $lines = explode("\n", $text);
    
    $scenarioData = [
        'title' => 'AI Generated Scenario',
        'description' => $text,
        'initial_conditions' => '',
        'challenges' => [],
        'expected_actions' => [],
        'safety_notes' => '',
        'learning_objectives' => []
    ];
    
    // Try to extract title (first line or first sentence)
    if (!empty($lines[0])) {
        $firstLine = trim($lines[0]);
        // Remove markdown headers if present
        $firstLine = preg_replace('/^#+\s*/', '', $firstLine);
        if (strlen($firstLine) < 100 && !empty($firstLine)) {
            $scenarioData['title'] = $firstLine;
        }
    }
    
    return $scenarioData;
}

/**
 * Validate and ensure all required fields are present in scenario data
 */
function validateScenarioData($data) {
    if (!is_array($data)) {
        $data = [];
    }
    
    // Ensure all required fields exist with defaults
    $validated = [
        'title' => $data['title'] ?? 'AI Generated Scenario',
        'description' => $data['description'] ?? '',
        'initial_conditions' => $data['initial_conditions'] ?? '',
        'challenges' => is_array($data['challenges'] ?? null) ? $data['challenges'] : [],
        'expected_actions' => is_array($data['expected_actions'] ?? null) ? $data['expected_actions'] : [],
        'safety_notes' => $data['safety_notes'] ?? '',
        'learning_objectives' => is_array($data['learning_objectives'] ?? null) ? $data['learning_objectives'] : []
    ];
    
    // Ensure arrays are not empty (at least have one item)
    if (empty($validated['challenges'])) {
        $validated['challenges'] = ['Address immediate safety concerns'];
    }
    if (empty($validated['expected_actions'])) {
        $validated['expected_actions'] = ['Assess the situation'];
    }
    if (empty($validated['learning_objectives'])) {
        $validated['learning_objectives'] = ['Improve disaster response coordination'];
    }
    
    return $validated;
}

