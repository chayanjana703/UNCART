<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Ai\AiChatService;
use App\Models\CustomerInquiry;
use App\Models\ActivityLog;
use App\Models\Vendor;
use App\Models\User;

class AiAssistantController extends Controller
{
    protected AiChatService $aiService;

    public function __construct(AiChatService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Handle incoming chat message from customer.
     */
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
        ]);

        $message = trim($validated['message']);
        $sessionHistory = session('ai_chat_history', []);
        $requestHistory = $validated['history'] ?? [];

        // Prefer request history if provided, else use session
        $history = !empty($requestHistory) ? $requestHistory : $sessionHistory;

        $user = Auth::user();
        $response = $this->aiService->chat($message, $history, $user);

        // Update history (keep last 12 messages)
        $history[] = ['role' => 'user', 'text' => $message];
        $history[] = ['role' => 'assistant', 'text' => $response['reply']];
        $history = array_slice($history, -12);

        session(['ai_chat_history' => $history]);

        return response()->json([
            'success' => true,
            'reply' => $response['reply'],
            'products' => $response['products'] ?? [],
            'comparison' => $response['comparison'] ?? null,
            'order' => $response['order'] ?? null,
            'actions' => $response['actions'] ?? [],
            'intent' => $response['intent'] ?? 'general',
            'provider' => $response['provider_used'] ?? 'smart_catalog',
            'provider_used' => $response['provider_used'] ?? 'smart_catalog',
        ]);
    }

    /**
     * Clear chat session history.
     */
    public function clear(Request $request)
    {
        session()->forget('ai_chat_history');

        return response()->json([
            'success' => true,
            'message' => 'Chat history cleared.',
            'default_prompts' => $this->aiService->getDefaultQuickPrompts(),
        ]);
    }

    /**
     * Connect customer to human employee by creating a support inquiry ticket.
     */
    public function handoff(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        $user = Auth::user();
        $userId = $user ? $user->id : (User::where('role', 'customer')->value('id') ?? User::value('id') ?? 1);
        $vendorId = Vendor::where('status', 'active')->value('id') ?? Vendor::value('id') ?? 1;

        $inquiry = CustomerInquiry::create([
            'user_id' => $userId,
            'vendor_id' => $vendorId,
            'subject' => $validated['subject'] ?? 'AI Concierge Support Request',
            'message' => $validated['message'],
            'status' => 'open',
        ]);

        if ($user) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'Support Inquiry Submitted via AI',
                'description' => "Created support ticket #{$inquiry->id}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return response()->json([
            'success' => true,
            'ticket_id' => $inquiry->id,
            'message' => "Your support request (#{$inquiry->id}) has been registered. Our concierge team will reach out promptly.",
            'support_phone' => config('ai.support_phone', '+1 (800) 892-0199'),
            'support_email' => config('ai.support_email', 'support@uncart.com'),
        ]);
    }

    /**
     * Retrieve starter suggestions for the widget.
     */
    public function suggestions()
    {
        return response()->json([
            'success' => true,
            'bot_name' => config('ai.bot_name', 'UNCART Concierge'),
            'prompts' => $this->aiService->getDefaultQuickPrompts(),
        ]);
    }
}
