# Gemini API Setup Guide

## 🚀 Quick Start

### Step 1: Get Your Gemini API Key

1. Go to [Google AI Studio](https://ai.google.dev/)
2. Sign in with your Google account
3. Click "Get API Key" or go to [API Keys page](https://aistudio.google.com/app/apikey)
4. Create a new API key
5. Copy the API key (you'll need it in Step 2)

### Step 2: Configure API Key

1. Open `api/config.php`
2. Find this line:
   ```php
   define('GEMINI_API_KEY', 'YOUR_GEMINI_API_KEY_HERE');
   ```
3. Replace `YOUR_GEMINI_API_KEY_HERE` with your actual API key:
   ```php
   define('GEMINI_API_KEY', 'AIzaSy...your-actual-key-here');
   ```
4. Save the file

### Step 3: Test the Integration

1. Go to the Scenario Design page
2. Click "🤖 Generate with AI" button
3. Fill in the form:
   - Select a disaster type (required)
   - Choose difficulty level
   - Select location type
   - Add any additional context (optional)
4. Click "Generate Scenario"
5. Wait for the AI to generate (usually 5-10 seconds)
6. Review the generated scenario and edit as needed

---

## 📋 API Endpoint Details

**Endpoint:** `/api/action/generate-scenario.php`  
**Method:** POST  
**Content-Type:** application/json

### Request Body:
```json
{
  "disaster_type": "earthquake",
  "difficulty": "intermediate",
  "location": "Barangay San Agustin, Novaliches, Quezon City",
  "incident_time": "day",
  "weather_condition": "sunny",
  "location_type": "building",
  "additional_context": "Include communication challenges"
}
```

### Response (Success):
```json
{
  "success": true,
  "message": "Scenario generated successfully",
  "data": {
    "title": "Earthquake Scenario Title",
    "description": "Detailed scenario description...",
    "initial_conditions": "...",
    "challenges": ["Challenge 1", "Challenge 2"],
    "expected_actions": ["Action 1", "Action 2"],
    "safety_notes": "...",
    "learning_objectives": ["Objective 1", "Objective 2"]
  }
}
```

### Response (Error):
```json
{
  "success": false,
  "error": "Error message here"
}
```

---

## 🔧 Configuration Options

### In `api/config.php`:

```php
// API Timeout (seconds)
define('API_TIMEOUT', 30);

// Max Retries
define('MAX_RETRIES', 3);

// Debug Mode (set to false in production)
define('DEBUG_MODE', true);
```

### Gemini API Generation Settings:

In `api/action/generate-scenario.php`, you can adjust:

```php
'generationConfig' => [
    'temperature' => 0.9,      // Creativity (0.0-1.0, higher = more creative)
    'topK' => 40,              // Diversity
    'topP' => 0.95,            // Nucleus sampling
    'maxOutputTokens' => 2048, // Max response length
]
```

---

## 🐛 Troubleshooting

### Error: "Gemini API key is not configured"
- **Solution:** Make sure you've set `GEMINI_API_KEY` in `api/config.php`

### Error: "Failed to generate scenario"
- **Possible causes:**
  1. Invalid API key
  2. API quota exceeded
  3. Network connectivity issues
  4. API endpoint changed

- **Solutions:**
  1. Verify your API key is correct
  2. Check your API quota at [Google AI Studio](https://aistudio.google.com/)
  3. Check internet connection
  4. Enable `DEBUG_MODE` in config.php to see detailed error logs

### API Response is not JSON
- The system has a fallback parser that will extract structured data from text responses
- If JSON extraction fails, it will create a basic structure from the text

### Slow Response Times
- Normal: 5-15 seconds
- If consistently slow:
  - Check your internet connection
  - Reduce `maxOutputTokens` if you don't need long responses
  - Consider caching common scenarios

---

## 💰 API Costs

**Free Tier:**
- Gemini API has a free tier with generous limits
- Check current limits at [Google AI Studio](https://aistudio.google.com/)

**Pricing:**
- Review current pricing at [Google AI Pricing](https://ai.google.dev/pricing)
- Monitor usage in Google Cloud Console

---

## 🔒 Security Best Practices

1. **Never commit API keys to Git**
   - Add `api/config.php` to `.gitignore` if it contains sensitive data
   - Or use environment variables

2. **Use Environment Variables (Recommended):**
   ```php
   // In config.php
   define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: 'YOUR_GEMINI_API_KEY_HERE');
   ```
   
   Then set in your server environment or `.env` file

3. **Restrict API Key Usage:**
   - In Google Cloud Console, restrict API key to specific IPs/domains
   - Set API key restrictions

4. **Monitor Usage:**
   - Regularly check API usage in Google Cloud Console
   - Set up billing alerts

---

## 📚 Additional Resources

- [Gemini API Documentation](https://ai.google.dev/docs)
- [Gemini API PHP Examples](https://github.com/google/generative-ai-php)
- [Google AI Studio](https://aistudio.google.com/)
- [API Pricing](https://ai.google.dev/pricing)

---

## ✅ Testing Checklist

- [ ] API key configured
- [ ] Can generate basic scenario
- [ ] Different disaster types work
- [ ] Error handling works (test with invalid key)
- [ ] Form populates correctly after generation
- [ ] Loading states work properly
- [ ] Success/error messages display correctly

---

## 🎯 Next Steps

After setting up:
1. Test with different disaster types
2. Test with different difficulty levels
3. Refine the prompt template if needed
4. Add database saving functionality
5. Implement scenario caching for common types

---

**Need Help?** Check the error logs (if DEBUG_MODE is enabled) or review the API response in browser developer tools.

