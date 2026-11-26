<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    | Options: 'grok', 'gemini', 'auto' (tries grok/gemini if keys present, else smart catalog)
    */
    'provider' => env('AI_PROVIDER', 'auto'),

    /*
    |--------------------------------------------------------------------------
    | xAI Grok Configuration
    |--------------------------------------------------------------------------
    */
    'grok' => [
        'api_key' => env('GROK_API_KEY', ''),
        'model' => env('GROK_MODEL', 'grok-2-latest'),
        'endpoint' => env('GROK_ENDPOINT', 'https://api.x.ai/v1/chat/completions'),
        'temperature' => 0.4,
        'max_tokens' => 1200,
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Gemini Configuration
    |--------------------------------------------------------------------------
    */
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', ''),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models/',
        'temperature' => 0.4,
        'max_tokens' => 1200,
    ],

    /*
    |--------------------------------------------------------------------------
    | Groq Cloud Configuration (Ultra-Fast LPU Engine)
    |--------------------------------------------------------------------------
    */
    'groq' => [
        'api_key' => env('GROQ_API_KEY', ''),
        'model' => env('GROQ_MODEL', 'qwen/qwen3.8-27b'),
        'endpoint' => env('GROQ_ENDPOINT', 'https://api.groq.com/openai/v1/chat/completions'),
        'temperature' => 0.4,
        'max_tokens' => 800,
    ],

    /*
    |--------------------------------------------------------------------------
    | NVIDIA NIM Fallback Configuration
    |--------------------------------------------------------------------------
    */
    'nvidia' => [
        'api_key' => env('NVIDIA_API_KEY', ''),
        'model' => env('NVIDIA_MODEL', 'nvidia/nemotron-3.5-lightning-30b-a3b'),
        'endpoint' => env('NVIDIA_ENDPOINT', 'https://integrate.api.nvidia.com/v1/chat/completions'),
        'temperature' => 0.4,
        'max_tokens' => 800,
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenRouter Configuration (Multi-Model Aggregator & Free Tier Models)
    |--------------------------------------------------------------------------
    */
    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY', ''),
        'model' => env('OPENROUTER_MODEL', 'inclusionai/ling-3.2-flash:free'),
        'endpoint' => env('OPENROUTER_ENDPOINT', 'https://openrouter.ai/api/v1/chat/completions'),
        'temperature' => 0.4,
        'max_tokens' => 800,
    ],

    /*
    |--------------------------------------------------------------------------
    | Bot Persona & Platform Knowledge
    |--------------------------------------------------------------------------
    */
    'bot_name' => env('AI_BOT_NAME', 'UNCART Concierge'),
    'platform_name' => 'UNCART Multi-Vendor E-Commerce Platform',
    'support_phone' => '+1 (800) 892-0199',
    'support_email' => 'support@uncart.com',
    'support_hours' => '24/7 Dedicated Concierge & Support',
];
