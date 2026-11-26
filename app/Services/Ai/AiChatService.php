<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;

class AiChatService
{
    /**
     * Process a chat prompt and return rich assistant response.
     */
    public function chat(string $message, array $history = [], ?User $user = null): array
    {
        $message = trim($message);
        if (empty($message)) {
            return [
                'reply' => "Hello! I am your UNCART AI Concierge. How may I assist you with products, technical comparisons, or order tracking today?",
                'products' => [],
                'comparison' => null,
                'order' => null,
                'actions' => $this->getDefaultQuickPrompts(),
                'intent' => 'general',
                'provider_used' => 'local',
            ];
        }

        // 1. Analyze Intent and Ground with Live Database
        $context = $this->buildDatabaseContext($message, $user);

        // 2. Try LLM Provider (Grok / Gemini) if configured
        $llmReply = null;
        $providerUsed = 'smart_catalog';

        $provider = config('ai.provider', 'auto');
        $grokKey = config('ai.grok.api_key');
        $geminiKey = config('ai.gemini.api_key');
        $groqKey = config('ai.groq.api_key');
        $nvidiaKey = config('ai.nvidia.api_key');
        $openrouterKey = config('ai.openrouter.api_key');

        // 1. Explicit provider choice if designated
        if ($provider === 'openrouter' && !empty($openrouterKey)) {
            $llmReply = $this->callOpenRouter($message, $history, $context);
            if ($llmReply) $providerUsed = 'openrouter';
        } elseif ($provider === 'groq' && !empty($groqKey)) {
            $llmReply = $this->callGroq($message, $history, $context);
            if ($llmReply) $providerUsed = 'groq';
        } elseif ($provider === 'gemini' && !empty($geminiKey)) {
            $llmReply = $this->callGemini($message, $history, $context);
            if ($llmReply) $providerUsed = 'gemini';
        } elseif ($provider === 'nvidia' && !empty($nvidiaKey)) {
            $llmReply = $this->callNvidia($message, $history, $context);
            if ($llmReply) $providerUsed = 'nvidia';
        } elseif ($provider === 'grok' && !empty($grokKey)) {
            $llmReply = $this->callGrok($message, $history, $context);
            if ($llmReply) $providerUsed = 'grok';
        }

        // 2. Primary auto tier: Google Gemini 2.5 Flash
        if (!$llmReply && !empty($geminiKey)) {
            $llmReply = $this->callGemini($message, $history, $context);
            if ($llmReply) $providerUsed = 'gemini';
        }

        // 3. Ultra-Fast Fallback tier: Groq LPU Engine
        if (!$llmReply && !empty($groqKey)) {
            $llmReply = $this->callGroq($message, $history, $context);
            if ($llmReply) $providerUsed = 'groq';
        }

        // 4. OpenRouter Free/Multimodal Tier
        if (!$llmReply && !empty($openrouterKey)) {
            $llmReply = $this->callOpenRouter($message, $history, $context);
            if ($llmReply) $providerUsed = 'openrouter';
        }

        // 5. Resilient Fallback tier: NVIDIA NIM
        if (!$llmReply && !empty($nvidiaKey)) {
            $llmReply = $this->callNvidia($message, $history, $context);
            if ($llmReply) $providerUsed = 'nvidia';
        }

        // 6. Secondary Failover tier: xAI Grok
        if (!$llmReply && !empty($grokKey)) {
            $llmReply = $this->callGrok($message, $history, $context);
            if ($llmReply) $providerUsed = 'grok';
        }

        // 7. Zero-downtime Fallback: Local Smart Catalog Engine
        if (!$llmReply) {
            $llmReply = $this->generateSmartCatalogResponse($message, $context, $user);
            $providerUsed = 'smart_catalog';
        }

        return [
            'reply' => $llmReply,
            'products' => $context['matched_products'],
            'comparison' => $context['comparison_data'],
            'order' => $context['order_data'],
            'actions' => $this->generateNextActions($context, $user),
            'intent' => $context['intent'],
            'provider_used' => $providerUsed,
        ];
    }

    /**
     * Retrieve relevant products, comparison matrices, coupons, and orders from database.
     */
    private function buildDatabaseContext(string $message, ?User $user = null): array
    {
        $lowerMsg = strtolower($message);
        $intent = 'general';

        $matchedProducts = [];
        $comparisonData = null;
        $orderData = null;

        // Detect Order Tracking query
        if (preg_match('/(order|track|shipment|package|delivery|ord-[a-z0-9]+)/i', $message)) {
            $intent = 'tracking';
            preg_match('/(ORD-[A-Z0-9]+)/i', $message, $orderMatch);
            $orderNumber = $orderMatch[1] ?? null;

            if ($orderNumber) {
                $orderQuery = Order::where('order_number', $orderNumber);
                if ($user && !$user->isAdmin()) {
                    $orderQuery->where('user_id', $user->id);
                }
                $foundOrder = $orderQuery->first();
                if ($foundOrder) {
                    $orderData = [
                        'order_number' => $foundOrder->order_number,
                        'status' => ucfirst($foundOrder->order_status ?? 'Processing'),
                        'payment_status' => ucfirst($foundOrder->payment_status ?? 'Paid'),
                        'total' => '$' . number_format($foundOrder->total_amount, 2),
                        'tracking_number' => $foundOrder->tracking_number ?? 'In preparation',
                        'carrier' => $foundOrder->carrier_name ?? 'Express Freight',
                        'track_url' => route('orders.track', $foundOrder->order_number, false),
                    ];
                }
            } elseif ($user) {
                $latestOrder = Order::where('user_id', $user->id)->latest()->first();
                if ($latestOrder) {
                    $orderData = [
                        'order_number' => $latestOrder->order_number,
                        'status' => ucfirst($latestOrder->order_status ?? 'Processing'),
                        'payment_status' => ucfirst($latestOrder->payment_status ?? 'Paid'),
                        'total' => '$' . number_format($latestOrder->total_amount, 2),
                        'tracking_number' => $latestOrder->tracking_number ?? 'In preparation',
                        'carrier' => $latestOrder->carrier_name ?? 'Express Freight',
                        'track_url' => route('orders.track', $latestOrder->order_number, false),
                    ];
                }
            }
        }

        // Detect Human Support / Complaint / Speak with Employee
        if (preg_match('/(human|agent|support|employee|person|representative|help center|complaint|talk to|speak to|contact)/i', $message)) {
            $intent = 'support';
        }

        // Detect Product Comparison Intent
        if (preg_match('/(compare|vs|versus|difference|better than|which is better|comparison)/i', $message)) {
            $intent = 'comparison';
        }

        // Price Filter Detection (e.g. "under $100", "below 200", "< 50")
        $maxPrice = null;
        if (preg_match('/(?:under|below|less than|up to|\<)\s*\$?(\d+(?:\.\d+)?)/i', $message, $priceMatches)) {
            $maxPrice = (float) $priceMatches[1];
        }

        // Query Products Catalog
        $query = Product::where('is_active', true)
            ->where('status', 'approved')
            ->with(['category', 'vendor']);

        if ($maxPrice) {
            $query->where(function ($q) use ($maxPrice) {
                $q->where('sale_price', '>', 0)->where('sale_price', '<=', $maxPrice)
                  ->orWhere(function ($sq) use ($maxPrice) {
                      $sq->whereNull('sale_price')->orWhere('sale_price', 0);
                  })->where('price', '<=', $maxPrice);
            });
        }

        // Keyword Matching
        $keywords = array_filter(explode(' ', preg_replace('/[^a-zA-Z0-9\s]/', '', $lowerMsg)), fn($w) => strlen($w) >= 3);
        $stopWords = ['the', 'and', 'for', 'with', 'what', 'which', 'best', 'find', 'show', 'tell', 'about', 'from', 'this', 'that', 'under', 'below', 'compare'];
        $cleanKeywords = array_diff($keywords, $stopWords);

        if (!empty($cleanKeywords)) {
            $query->where(function ($q) use ($cleanKeywords) {
                foreach ($cleanKeywords as $kw) {
                    $q->orWhere('name', 'like', "%{$kw}%")
                      ->orWhere('short_description', 'like', "%{$kw}%")
                      ->orWhereHas('category', function ($cq) use ($kw) {
                          $cq->where('name', 'like', "%{$kw}%");
                      });
                }
            });
        }

        $results = $query->orderByDesc('rating_avg')
            ->orderByDesc('sales_count')
            ->take(6)
            ->get();

        // If no direct keyword match and intent isn't tracking, return top trending products
        if ($results->isEmpty() && $intent !== 'tracking' && $intent !== 'support') {
            $results = Product::where('is_active', true)
                ->where('status', 'approved')
                ->with(['category', 'vendor'])
                ->orderByDesc('rating_avg')
                ->take(3)
                ->get();
        }

        // If comparison requested but fewer than 2 matched, augment with other top catalog items
        if ($intent === 'comparison' && $results->count() < 2) {
            $more = Product::where('is_active', true)
                ->where('status', 'approved')
                ->whereNotIn('id', $results->pluck('id'))
                ->with(['category', 'vendor'])
                ->orderByDesc('rating_avg')
                ->take(2 - $results->count())
                ->get();
            $results = $results->concat($more);
        }

        // Format matched products
        foreach ($results as $p) {
            $matchedProducts[] = [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'url' => route('products.show', $p->slug, false),
                'price' => (float) $p->price,
                'current_price' => (float) $p->current_price,
                'is_on_sale' => $p->discount_percentage > 0,
                'discount_percent' => $p->discount_percentage,
                'image' => $p->featured_image,
                'category' => $p->category->name ?? 'Merchandise',
                'vendor_name' => $p->vendor->shop_name ?? 'Certified Merchant',
                'rating_avg' => (float) ($p->rating_avg ?? 5.0),
                'rating_count' => (int) ($p->rating_count ?? 1),
                'stock' => (int) $p->stock_quantity,
                'specs' => is_array($p->specifications) ? array_slice($p->specifications, 0, 4) : [],
            ];
        }

        // Build comparison data if intent is comparison and we have 2+ products
        if ($intent === 'comparison' && count($matchedProducts) >= 2) {
            $comparisonData = [
                'items' => array_slice($matchedProducts, 0, 3),
                'compare_all_url' => route('products.compare', [], false),
            ];
        }

        // Active coupon info
        try {
            $activeCoupons = Coupon::where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                })
                ->pluck('code')
                ->take(2)
                ->toArray();
        } catch (\Throwable $e) {
            $activeCoupons = ['WELCOME10'];
        }

        return [
            'intent' => $intent,
            'max_price' => $maxPrice,
            'matched_products' => $matchedProducts,
            'comparison_data' => $comparisonData,
            'order_data' => $orderData,
            'active_coupons' => !empty($activeCoupons) ? $activeCoupons : ['WELCOME10'],
        ];
    }

    /**
     * Call xAI Grok API.
     */
    private function callGrok(string $message, array $history, array $context): ?string
    {
        try {
            $endpoint = config('ai.grok.endpoint');
            $apiKey = config('ai.grok.api_key');
            $model = config('ai.grok.model', 'grok-2-latest');

            $systemPrompt = $this->buildSystemPrompt($context);

            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
            ];

            // Add previous recent messages
            foreach (array_slice($history, -6) as $h) {
                if (!empty($h['role']) && !empty($h['text'])) {
                    $messages[] = [
                        'role' => $h['role'] === 'user' ? 'user' : 'assistant',
                        'content' => $h['text'],
                    ];
                }
            }

            $messages[] = ['role' => 'user', 'content' => $message];

            $response = Http::timeout(12)
                ->withoutVerifying()
                ->withToken($apiKey)
                ->post($endpoint, [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.4,
                    'max_tokens' => 800,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            }

            Log::warning('Grok API responded with error: ' . $response->status() . ' - ' . $response->body());
        } catch (\Throwable $e) {
            Log::warning('Grok API call failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Call Google Gemini API.
     */
    private function callGemini(string $message, array $history, array $context): ?string
    {
        try {
            $apiKey = config('ai.gemini.api_key');
            $model = config('ai.gemini.model', 'gemini-2.5-flash');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $systemPrompt = $this->buildSystemPrompt($context);

            $contents = [];
            // Add system instruction
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => "[System Instructions & Catalog Context]:\n" . $systemPrompt]],
            ];
            $contents[] = [
                'role' => 'model',
                'parts' => [['text' => "Understood. I am UNCART AI Concierge, ready to assist customers accurately with live platform data."]],
            ];

            foreach (array_slice($history, -4) as $h) {
                if (!empty($h['role']) && !empty($h['text'])) {
                    $contents[] = [
                        'role' => $h['role'] === 'user' ? 'user' : 'model',
                        'parts' => [['text' => $h['text']]],
                    ];
                }
            }

            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $message]],
            ];

            $response = Http::timeout(12)
                ->withoutVerifying()
                ->post($url, [
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.4,
                        'maxOutputTokens' => 800,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }

            Log::warning('Gemini API responded with error: ' . $response->status() . ' - ' . $response->body());
        } catch (\Throwable $e) {
            Log::warning('Gemini API call failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Call Groq Cloud API (Ultra-Fast LPU Engine).
     */
    private function callGroq(string $message, array $history, array $context): ?string
    {
        try {
            $apiKey = config('ai.groq.api_key');
            if (empty($apiKey)) return null;

            $endpoint = config('ai.groq.endpoint', 'https://api.groq.com/openai/v1/chat/completions');
            $model = config('ai.groq.model', 'qwen/qwen3.8-27b');

            $systemPrompt = $this->buildSystemPrompt($context);

            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
            ];

            foreach (array_slice($history, -4) as $h) {
                if (!empty($h['role']) && !empty($h['text'])) {
                    $messages[] = [
                        'role' => $h['role'] === 'user' ? 'user' : 'assistant',
                        'content' => $h['text'],
                    ];
                }
            }

            $messages[] = ['role' => 'user', 'content' => $message];

            $response = Http::timeout(8)
                ->withoutVerifying()
                ->withToken($apiKey)
                ->post($endpoint, [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.4,
                    'max_tokens' => 800,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? null;
                if (!empty($content)) {
                    return trim($content);
                }
            }

            Log::warning('Groq API error: ' . $response->status() . ' - ' . $response->body());
        } catch (\Throwable $e) {
            Log::warning('Groq API call failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Call NVIDIA NIM API (Automatic Failover Model).
     */
    private function callNvidia(string $message, array $history, array $context): ?string
    {
        try {
            $apiKey = config('ai.nvidia.api_key');
            if (empty($apiKey)) return null;

            $endpoint = config('ai.nvidia.endpoint', 'https://integrate.api.nvidia.com/v1/chat/completions');
            $model = config('ai.nvidia.model', 'nvidia/nemotron-3.5-lightning-30b-a3b');

            $systemPrompt = $this->buildSystemPrompt($context);

            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
            ];

            foreach (array_slice($history, -4) as $h) {
                if (!empty($h['role']) && !empty($h['text'])) {
                    $messages[] = [
                        'role' => $h['role'] === 'user' ? 'user' : 'assistant',
                        'content' => $h['text'],
                    ];
                }
            }

            $messages[] = ['role' => 'user', 'content' => $message];

            $response = Http::timeout(12)
                ->withoutVerifying()
                ->withToken($apiKey)
                ->post($endpoint, [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.4,
                    'max_tokens' => 800,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $msg = $data['choices'][0]['message'] ?? [];
                if (!empty($msg['content'])) {
                    return trim($msg['content']);
                }
                if (!empty($msg['reasoning_content'])) {
                    return trim($msg['reasoning_content']);
                }
            }

            Log::warning('NVIDIA API responded with error: ' . $response->status() . ' - ' . $response->body());
        } catch (\Throwable $e) {
            Log::warning('NVIDIA API call failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Call OpenRouter API (Supports free models e.g. inclusionai/ling-3.2-flash:free).
     */
    private function callOpenRouter(string $message, array $history, array $context): ?string
    {
        try {
            $apiKey = config('ai.openrouter.api_key');
            if (empty($apiKey)) return null;

            $endpoint = config('ai.openrouter.endpoint', 'https://openrouter.ai/api/v1/chat/completions');
            $model = config('ai.openrouter.model', 'inclusionai/ling-3.2-flash:free');

            $systemPrompt = $this->buildSystemPrompt($context);

            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
            ];

            foreach (array_slice($history, -4) as $h) {
                if (!empty($h['role']) && !empty($h['text'])) {
                    $messages[] = [
                        'role' => $h['role'] === 'user' ? 'user' : 'assistant',
                        'content' => $h['text'],
                    ];
                }
            }

            $messages[] = ['role' => 'user', 'content' => $message];

            $response = Http::timeout(15)
                ->withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'HTTP-Referer' => config('app.url', 'http://localhost'),
                    'X-Title' => config('ai.platform_name', 'UNCART'),
                ])
                ->post($endpoint, [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.4,
                    'max_tokens' => 800,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $msg = $data['choices'][0]['message'] ?? [];
                if (!empty($msg['content'])) {
                    return trim($msg['content']);
                }
                if (!empty($msg['reasoning_content'])) {
                    return trim($msg['reasoning_content']);
                }
                if (!empty($msg['reasoning'])) {
                    return trim($msg['reasoning']);
                }
            }

            Log::warning('OpenRouter API responded with error: ' . $response->status() . ' - ' . $response->body());
        } catch (\Throwable $e) {
            Log::warning('OpenRouter API call failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Build grounded system prompt for LLMs.
     */
    private function buildSystemPrompt(array $context): string
    {
        $botName = config('ai.bot_name', 'UNCART Concierge');
        $phone = config('ai.support_phone', '+1 (800) 892-0199');
        $email = config('ai.support_email', 'support@uncart.com');

        $prompt = "You are {$botName}, an intelligent, highly knowledgeable, and polite shopping concierge for UNCART — a premium multi-vendor e-commerce platform.\n\n";
        $prompt .= "YOUR GOALS:\n";
        $prompt .= "1. Help customers discover, select, and compare products based on real catalog data.\n";
        $prompt .= "2. Explain technical specifications, materials, build quality, and pricing differences clearly.\n";
        $prompt .= "3. If user asks about an order, refer to the provided order context.\n";
        $prompt .= "4. If user asks to speak with a human employee or customer support, warmly share support phone {$phone}, email {$email}, and inform them that an inquiry ticket can be created.\n";
        $prompt .= "5. Mention active discount coupons when relevant (e.g. code WELCOME10 for 10% off).\n";
        $prompt .= "6. Keep answers concise, formatted with clear markdown bullet points and bold highlights.\n\n";

        if (!empty($context['matched_products'])) {
            $prompt .= "REAL CATALOG PRODUCTS CURRENTLY IN STOCK:\n";
            foreach ($context['matched_products'] as $p) {
                $priceStr = $p['is_on_sale'] ? "\${$p['current_price']} (Sale, originally \${$p['price']})" : "\${$p['price']}";
                $prompt .= "- {$p['name']} | Price: {$priceStr} | Category: {$p['category']} | Rating: {$p['rating_avg']}/5.0 | Seller: {$p['vendor_name']} | URL: {$p['url']}\n";
            }
            $prompt .= "\n";
        }

        if (!empty($context['order_data'])) {
            $o = $context['order_data'];
            $prompt .= "CUSTOMER ORDER DETAILS FOUND:\n";
            $prompt .= "- Order #: {$o['order_number']}\n- Status: {$o['status']}\n- Total: {$o['total']}\n- Carrier: {$o['carrier']}\n- Tracking #: {$o['tracking_number']}\n\n";
        }

        return $prompt;
    }

    /**
     * High-speed, zero-downtime local smart catalog response generator.
     */
    private function generateSmartCatalogResponse(string $message, array $context, ?User $user = null): string
    {
        $botName = config('ai.bot_name', 'UNCART Concierge');
        $intent = $context['intent'];
        $products = $context['matched_products'];
        $order = $context['order_data'];
        $phone = config('ai.support_phone', '+1 (800) 892-0199');
        $email = config('ai.support_email', 'support@uncart.com');

        // 1. Order Tracking Response
        if ($intent === 'tracking') {
            if ($order) {
                return "📦 **Order Status Found!**\n\n" .
                       "Here are the latest tracking details for **{$order['order_number']}**:\n" .
                       "- **Order Status**: `{$order['status']}`\n" .
                       "- **Payment**: `{$order['payment_status']}`\n" .
                       "- **Total Amount**: **{$order['total']}**\n" .
                       "- **Carrier & Tracking**: `{$order['carrier']}` — `{$order['tracking_number']}`\n\n" .
                       "You can view your complete timeline and official invoice directly by clicking the link below.";
            }

            return "📦 I'd be glad to track your shipment! To view live delivery updates, please provide your **Order Number** (e.g. `ORD-K15SP419`), or visit your [Orders Dashboard](" . route('orders.index', [], false) . ") if you are signed in.";
        }

        // 2. Human Support / Employee Contact Response
        if ($intent === 'support') {
            return "👋 **UNCART Support & Human Concierge**\n\n" .
                   "Our team of customer experience specialists is available to assist you directly:\n\n" .
                   "- 📞 **Direct Support Line**: **{$phone}** (24/7 Toll-Free)\n" .
                   "- ✉️ **Dedicated Email**: `{$email}`\n" .
                   "- 💬 **Instant Help Ticket**: You can submit a direct question through our [Support Center](" . route('static.contact', [], false) . ").\n\n" .
                   "Would you like me to open a prioritized support ticket with our customer care desk right now?";
        }

        // 3. Product Comparison Response
        if ($intent === 'comparison' && count($products) >= 2) {
            $p1 = $products[0];
            $p2 = $products[1];

            $reply = "⚖️ **Product Comparison: {$p1['name']} vs. {$p2['name']}**\n\n";
            $reply .= "Here is a quick head-to-head breakdown:\n\n";
            $reply .= "| Specification | **{$p1['name']}** | **{$p2['name']}** |\n";
            $reply .= "| :--- | :--- | :--- |\n";
            $reply .= "| **Current Price** | \${$p1['current_price']} | \${$p2['current_price']} |\n";
            $reply .= "| **Category** | {$p1['category']} | {$p2['category']} |\n";
            $reply .= "| **Rating** | ★ {$p1['rating_avg']} ({$p1['rating_count']}) | ★ {$p2['rating_avg']} ({$p2['rating_count']}) |\n";
            $reply .= "| **Merchant** | {$p1['vendor_name']} | {$p2['vendor_name']} |\n\n";

            if ($p1['current_price'] < $p2['current_price']) {
                $diff = number_format($p2['current_price'] - $p1['current_price'], 2);
                $reply .= "💡 **Value Insight**: **{$p1['name']}** is \${$diff} more affordable, making it a great budget-to-performance choice. Meanwhile, **{$p2['name']}** offers enhanced specifications and premium finishes.";
            } else {
                $diff = number_format($p1['current_price'] - $p2['current_price'], 2);
                $reply .= "💡 **Value Insight**: **{$p2['name']}** saves you \${$diff}, while **{$p1['name']}** delivers top-tier craftsmanship and higher customer ratings.";
            }

            return $reply;
        }

        // 4. Product Recommendations
        if (!empty($products)) {
            $count = count($products);
            $reply = "✨ **Found {$count} curated recommendation" . ($count > 1 ? 's' : '') . " for you:**\n\n";

            foreach ($products as $p) {
                $priceStr = $p['is_on_sale'] ? "**\${$p['current_price']}** ~(\${$p['price']})~" : "**\${$p['current_price']}**";
                $reply .= "- **[{$p['name']}]({$p['url']})** — {$priceStr}\n";
                $reply .= "  *★ {$p['rating_avg']} · {$p['category']} by {$p['vendor_name']}*\n";
            }

            $coupon = $context['active_coupons'][0] ?? 'WELCOME10';
            $reply .= "\n🎁 *Tip: Use coupon code **`{$coupon}`** at checkout for an instant discount on your order.*";

            return $reply;
        }

        // 5. Default Friendly Guide
        return "👋 Hello! I am your **{$botName}**.\n\n" .
               "I can assist you with:\n" .
               "- 🔍 **Finding items**: Tell me what you're looking for (e.g., *'mechanical keyboards under \$150'*).\n" .
               "- ⚖️ **Comparing specs**: Ask me to compare any two products side-by-side.\n" .
               "- 📦 **Order tracking**: Type *'track order ORD-XXXX'* for live shipping status.\n" .
               "- 💬 **Human Support**: Ask for an employee to get direct contact numbers.\n\n" .
               "What can I help you discover today?";
    }

    /**
     * Quick follow-up actions and chip suggestions.
     */
    private function generateNextActions(array $context, ?User $user = null): array
    {
        $actions = [];

        if ($context['intent'] === 'comparison' && !empty($context['comparison_data'])) {
            $actions[] = [
                'label' => 'Open Full Comparison Tray',
                'action' => 'navigate',
                'url' => route('products.compare', [], false),
                'icon' => '⚖️',
            ];
        }

        if ($context['intent'] === 'support') {
            $actions[] = [
                'label' => 'Call +1 (800) 892-0199',
                'action' => 'tel',
                'url' => 'tel:+18008920199',
                'icon' => '📞',
            ];
            $actions[] = [
                'label' => 'Open Support Ticket',
                'action' => 'support_modal',
                'url' => route('static.contact', [], false),
                'icon' => '✉️',
            ];
        }

        if (!empty($context['order_data'])) {
            $actions[] = [
                'label' => 'View Live Parcel Tracking',
                'action' => 'navigate',
                'url' => $context['order_data']['track_url'],
                'icon' => '🚚',
            ];
        }

        return $actions;
    }

    /**
     * Default starting prompts for new chat sessions.
     */
    public function getDefaultQuickPrompts(): array
    {
        return [
            ['label' => '🎧 Best headphones under $200', 'query' => 'Show me best headphones under 200'],
            ['label' => '⌨️ Mechanical keyboards', 'query' => 'Find top rated mechanical keyboards'],
            ['label' => '⚖️ Compare desk accessories', 'query' => 'Compare walnut desk shelf vs monitor stand'],
            ['label' => '📦 Track my recent order', 'query' => 'Track my recent order'],
            ['label' => '👤 Talk to human support', 'query' => 'I want to speak with customer support'],
        ];
    }
}
