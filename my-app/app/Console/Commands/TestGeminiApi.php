<?php

namespace App\Console\Commands;

use App\Services\GeminiService;
use Illuminate\Console\Command;

class TestGeminiApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gemini:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Gemini API connection and list available models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Gemini API connection...');
        $this->line('');

        $service = new GeminiService();
        $models = $service->listAvailableModels();

        if (empty($models)) {
            $this->error('❌ No models found or API connection failed.');
            $this->line('');
            $this->line('Please check:');
            $this->line('1. GEMINI_API_KEY is set in .env or config/services.php');
            $this->line('2. The API key is valid');
            $this->line('3. The Generative Language API is enabled for your project');
            return Command::FAILURE;
        }

        $this->info('✅ Found ' . count($models) . ' model(s):');
        $this->line('');

        $tableData = [];
        foreach ($models as $model) {
            $name = $model['name'] ?? 'Unknown';
            $version = $model['api_version'] ?? '?';
            $methods = $model['supportedGenerationMethods'] ?? [];
            $supportsGenerate = in_array('generateContent', $methods) ? '✅' : '❌';
            
            $tableData[] = [
                'Name' => $name,
                'API Version' => $version,
                'Supports generateContent' => $supportsGenerate,
                'Methods' => implode(', ', $methods),
            ];
        }

        $this->table(
            ['Name', 'API Version', 'Supports generateContent', 'Methods'],
            $tableData
        );

        $this->line('');
        $this->info('Testing scenario generation...');
        
        try {
            $result = $service->generateScenarioFromPrompt(
                'A small earthquake in a residential area',
                'Earthquake',
                'Basic'
            );
            
            $this->info('✅ Scenario generation successful!');
            $this->line('');
            $this->line('Generated scenario:');
            $this->line('Title: ' . ($result['title'] ?? 'N/A'));
            $this->line('Disaster Type: ' . ($result['disaster_type'] ?? 'N/A'));
            $this->line('Difficulty: ' . ($result['difficulty'] ?? 'N/A'));
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Scenario generation failed:');
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
