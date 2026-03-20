<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\AboutSection;
use App\Models\Service;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'Gemini API Key not configured'], 500);
        }

        $userMessage = $request->input('message');
        $history = $request->input('history', []);

        // Build context from database
        $settings = SiteSetting::first();
        $about = AboutSection::first();
        $services = Service::where('is_active', true)->get()->pluck('title_id')->implode(', ');
        $projects = Project::all()->pluck('title_id')->implode(', ');

        $systemPrompt = "You are FKBot, a friendly and professional AI assistant for FKStudio.
            Company Info: " . ($about->content_id ?? 'FKStudio is a digital agency focusing on innovation.') . "
            Our Services: {$services}.
            Recent Projects: {$projects}.
            Contact No (WhatsApp): {$settings->contact_phone}.
            
            Rules:
            1. Be concise.
            2. Answer in the user's language.
            3. CRITICAL: If the user asks for 'admin', 'whatsapp', 'pesan proyek', or detailed pricing/consultation, tell them to chat on WhatsApp. Mention the word 'WhatsApp' or 'Admin' specifically so the UI can show the button.
            4. Do not repeat the same greeting if you are already in a conversation.";

        $contents = [];
        // Extract history consistently
        if (is_array($history)) {
            foreach ($history as $msg) {
                if (isset($msg['role']) && isset($msg['text'])) {
                    $contents[] = [
                        'role' => ($msg['role'] === 'user') ? 'user' : 'model',
                        'parts' => [['text' => $msg['text']]]
                    ];
                }
            }
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

        try {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$apiKey}", [
                'contents' => $contents,
                'system_instruction' => [
                    'parts' => [['text' => $systemPrompt]]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $botResponse = $response->json('candidates.0.content.parts.0.text');
                return response()->json(['message' => $botResponse]);
            }

            Log::error('Gemini API Error: ' . $response->body());
            return response()->json(['error' => 'Failed to get response from AI'], 500);

        } catch (\Exception $e) {
            Log::error('Chat Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
