<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private $apiKey;
    private $modelName;
    private $apiVersions = ['v1beta', 'v1'];
    private $currentApiVersion = 'v1beta';
    private $baseUrl = 'https://generativelanguage.googleapis.com';
    private $availableModels = [
        'gemini-2.5-flash',
        'gemini-2.5-pro',
        'gemini-2.0-flash',
        'gemini-flash-latest',
        'gemini-pro-latest',
        'gemini-1.5-pro',
        'gemini-1.5-flash',
        'gemini-pro',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->modelName = $this->findAvailableModel();
    }

    /**
     * Find an available model by listing models from the API
     */
    private function findAvailableModel(): string
    {
        if (!$this->apiKey) {
            return 'gemini-2.5-flash'; // Default fallback
        }

        // Try to list models first (try both API versions)
        foreach ($this->apiVersions as $version) {
            try {
                $url = $this->baseUrl . '/' . $version . '/models?key=' . $this->apiKey;
                $response = Http::timeout(10)->get($url);
                
                if ($response->successful()) {
                    $this->currentApiVersion = $version;
                    $data = $response->json();
                    
                    if (isset($data['models']) && is_array($data['models'])) {
                        // Update available models list with what's actually available
                        $this->availableModels = [];
                        
                        foreach ($data['models'] as $model) {
                            $modelName = $model['name'] ?? '';
                            // Remove 'models/' prefix
                            if (strpos($modelName, 'models/') === 0) {
                                $modelName = substr($modelName, 7);
                            }
                            
                            // Check if model supports generateContent
                            $supportedMethods = $model['supportedGenerationMethods'] ?? [];
                            if (in_array('generateContent', $supportedMethods)) {
                                $this->availableModels[] = $modelName;
                                Log::info("Found available Gemini model: {$modelName} (supports generateContent)");
                            }
                        }
                        
                        // Use the first available model
                        if (!empty($this->availableModels)) {
                            $selectedModel = $this->availableModels[0];
                            Log::info("Using Gemini model: {$selectedModel} with API version: {$version}");
                            return $selectedModel;
                        }
                    }
                } else {
                    Log::warning("Failed to list models with {$version}", [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning("Could not list Gemini models with {$version}", ['error' => $e->getMessage()]);
            }
        }

        // Default fallback if listing fails (use newer model)
        Log::warning("Could not determine available models, using default: gemini-2.5-flash");
        return 'gemini-2.5-flash';
    }

    /**
     * Generate a disaster scenario using Gemini API from custom user prompt
     */
    public function generateScenarioFromPrompt(string $userPrompt, string $disasterType = null, string $difficulty = 'Medium'): array
    {
        if (!$this->apiKey) {
            throw new \Exception('Gemini API key not configured. Add GEMINI_API_KEY to .env');
        }

        $prompt = $this->buildScenarioPromptFromUserInput($userPrompt, $disasterType, $difficulty);

        try {
            // Refresh available models if we haven't found any yet
            if (empty($this->availableModels)) {
                $this->modelName = $this->findAvailableModel();
            }
            
            // Try the primary model first, then fallback to others if needed
            $modelsToTry = [$this->modelName];
            foreach ($this->availableModels as $model) {
                if ($model !== $this->modelName && !in_array($model, $modelsToTry)) {
                    $modelsToTry[] = $model;
                }
            }
            
            // If we still don't have models, add defaults (newer models first)
            if (empty($modelsToTry) || (count($modelsToTry) === 1 && empty($this->availableModels))) {
                $modelsToTry = array_merge($modelsToTry, [
                    'gemini-2.5-flash',
                    'gemini-2.5-pro',
                    'gemini-2.0-flash',
                    'gemini-flash-latest',
                    'gemini-pro-latest',
                ]);
            }

            $lastError = null;
            $response = null;
            $successfulModel = null;

            // Try each model with each API version
            foreach ($this->apiVersions as $apiVersion) {
                foreach ($modelsToTry as $model) {
                    try {
                        $url = $this->baseUrl . '/' . $apiVersion . '/models/' . $model . ':generateContent?key=' . $this->apiKey;
                        
                        $response = Http::timeout(60)
                            ->post($url, [
                                'contents' => [
                                    [
                                        'parts' => [
                                            ['text' => $prompt]
                                        ]
                                    ]
                                ]
                            ]);

                        if ($response->successful()) {
                            // Success! Update model name and API version for future use
                            if ($model !== $this->modelName || $apiVersion !== $this->currentApiVersion) {
                                $this->modelName = $model;
                                $this->currentApiVersion = $apiVersion;
                                Log::info("Using Gemini model: {$model} with API version: {$apiVersion}");
                            }
                            $successfulModel = $model;
                            break 2; // Exit both loops
                        }

                        $errorBody = $response->json();
                        $errorMessage = $errorBody['error']['message'] ?? $response->body();
                        $lastError = $errorMessage;

                        // If it's a 404 (model not found), try next model/version
                        if ($response->status() === 404 && strpos($errorMessage, 'not found') !== false) {
                            Log::warning("Model {$model} not found with {$apiVersion}, trying next");
                            continue; // Try next model
                        }

                        // For other errors, try next model/version
                        Log::warning("Gemini API error with {$model} ({$apiVersion}): {$errorMessage}");
                        continue;

                    } catch (\Exception $e) {
                        // If it's a 404, continue to next model/version
                        if (strpos($e->getMessage(), 'not found') !== false || strpos($e->getMessage(), '404') !== false) {
                            $lastError = $e->getMessage();
                            continue;
                        }
                        // For other exceptions, log and continue
                        Log::warning("Exception with {$model} ({$apiVersion}): " . $e->getMessage());
                        $lastError = $e->getMessage();
                        continue;
                    }
                }
            }

            // Check if we got a successful response
            if (!$response || !$response->successful() || !$successfulModel) {
                // Try to get available models for better error message
                $availableModels = $this->listAvailableModels();
                $modelNames = array_map(function($m) {
                    return ($m['name'] ?? 'unknown') . ' (v' . ($m['api_version'] ?? '?') . ')';
                }, $availableModels);
                
                $errorMsg = 'All Gemini models failed. Last error: ' . ($lastError ?? 'Unknown error');
                if (!empty($modelNames)) {
                    $errorMsg .= "\n\nAvailable models: " . implode(', ', array_slice($modelNames, 0, 5));
                } else {
                    $errorMsg .= "\n\nCould not retrieve list of available models. Please check your API key and ensure the Generative Language API is enabled.";
                }
                
                throw new \Exception($errorMsg);
            }

            $data = $response->json();
            
            // Extract text from Gemini response
            $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            if (empty($generatedText)) {
                throw new \Exception('Empty response from Gemini API');
            }
            
            return $this->parseScenarioFromText($generatedText, $disasterType);

        } catch (\Exception $e) {
            Log::error('Gemini scenario generation failed', [
                'error' => $e->getMessage(),
                'prompt' => $userPrompt,
                'disaster_type' => $disasterType
            ]);
            throw $e;
        }
    }

    /**
     * Get list of available models (for debugging)
     */
    public function listAvailableModels(): array
    {
        if (!$this->apiKey) {
            return [];
        }

        $allModels = [];
        
        foreach ($this->apiVersions as $version) {
            try {
                $url = $this->baseUrl . '/' . $version . '/models?key=' . $this->apiKey;
                $response = Http::timeout(10)->get($url);
                
                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['models'])) {
                        foreach ($data['models'] as $model) {
                            $model['api_version'] = $version;
                            $allModels[] = $model;
                        }
                    }
                } else {
                    Log::warning("Failed to list models with {$version}", [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Exception listing models with {$version}", ['error' => $e->getMessage()]);
            }
        }

        return $allModels;
    }

    /**
     * Legacy method for backward compatibility
     */
    public function generateScenario(string $disasterType, string $difficulty = 'Medium'): array
    {
        return $this->generateScenarioFromPrompt("Create a {$difficulty} difficulty scenario for {$disasterType}", $disasterType, $difficulty);
    }

    /**
     * Build the prompt for Gemini to generate a scenario from user input
     */
    private function buildScenarioPromptFromUserInput(string $userPrompt, ?string $disasterType, string $difficulty): string
    {
        $disasterTypeText = $disasterType ? "Disaster Type: {$disasterType}\n" : "";
        
        $prompt = "You are a disaster preparedness training expert. Based on the following user description, generate a comprehensive disaster response training scenario.

User's Scenario Description: {$userPrompt}
{$disasterTypeText}Difficulty Level: {$difficulty}

Return the response ONLY as valid JSON (no markdown, no code blocks, no explanations, just the JSON object):
{
  \"title\": \"Brief, descriptive scenario title (max 100 chars)\",
  \"short_description\": \"2-3 sentence overview of the scenario\",
  \"affected_area\": \"Specific location/barangay name\",
  \"incident_time_text\": \"Time in HH:MM AM/PM format (e.g., 10:17 AM)\",
  \"general_situation\": \"Detailed description of the disaster situation, what's happening, current state, and context\",
  \"severity_level\": \"Low\" or \"Medium\" or \"High\" or \"Critical\",
  \"difficulty\": \"{$difficulty}\",
  \"intended_participants\": \"Target roles/positions (e.g., Barangay Officials, Fire Responders, Medical Teams, Students, Staff, Volunteers)\",
  \"injured_victims_count\": integer_number,
  \"trapped_persons_count\": integer_number,
  \"infrastructure_damage\": \"Description of damaged infrastructure, buildings, roads, utilities\",
  \"communication_status\": \"working\" or \"unstable\" or \"down\"
}

Important:
- All string values must be properly escaped JSON strings
- Numbers must be integers (no decimals)
- Severity level and communication status must match the exact values provided
- Make the scenario realistic, detailed, and appropriate for disaster preparedness training
- Base all details on the user's description while making it comprehensive";

        return $prompt;
    }

    /**
     * Parse the generated text into scenario data
     */
    private function parseScenarioFromText(string $text, ?string $disasterType): array
    {
        // Clean the text - remove markdown code blocks if present
        $cleanText = $text;
        $cleanText = preg_replace('/```json\s*/i', '', $cleanText);
        $cleanText = preg_replace('/```\s*/', '', $cleanText);
        $cleanText = trim($cleanText);
        
        // Try to find JSON object
        $jsonMatch = preg_match('/\{[\s\S]*\}/', $cleanText, $matches);
        
        if (!$jsonMatch) {
            Log::error('Could not extract JSON from Gemini response', ['text' => $text]);
            throw new \Exception('Could not extract JSON from AI response. The AI may have returned invalid format.');
        }

        $json = json_decode($matches[0], true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON in Gemini response', [
                'error' => json_last_error_msg(),
                'text' => $matches[0]
            ]);
            throw new \Exception('Invalid JSON in AI response: ' . json_last_error_msg());
        }

        // Validate severity_level and communication_status
        $severityLevel = $json['severity_level'] ?? 'Medium';
        if (!in_array($severityLevel, ['Low', 'Medium', 'High', 'Critical'])) {
            $severityLevel = 'Medium';
        }

        $communicationStatus = $json['communication_status'] ?? 'working';
        if (!in_array($communicationStatus, ['working', 'unstable', 'down'])) {
            $communicationStatus = 'working';
        }

        // Ensure all required fields are present
        return [
            'title' => $json['title'] ?? 'Generated Scenario',
            'short_description' => $json['short_description'] ?? '',
            'affected_area' => $json['affected_area'] ?? '',
            'incident_time_text' => $json['incident_time_text'] ?? '',
            'general_situation' => $json['general_situation'] ?? '',
            'severity_level' => $severityLevel,
            'difficulty' => $json['difficulty'] ?? 'Medium',
            'intended_participants' => $json['intended_participants'] ?? '',
            'injured_victims_count' => max(0, (int)($json['injured_victims_count'] ?? 0)),
            'trapped_persons_count' => max(0, (int)($json['trapped_persons_count'] ?? 0)),
            'infrastructure_damage' => $json['infrastructure_damage'] ?? '',
            'communication_status' => $communicationStatus,
            'disaster_type' => $disasterType ?? '',
        ];
    }
}
