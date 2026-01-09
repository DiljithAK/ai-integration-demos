<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function summarize(Request $request)
    {
        $request->validate([
            'text' => 'required|string'
        ]);

        $url = config('services.ai.url');
        $token = config('services.ai.token');

        if (!$url || !$token) {
            return response()->json([
                'error' => 'AI configuration missing'
            ], 500);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type'  => 'application/json',
        ])->post($url, [
            'model' => 'microsoft/Phi-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Summarize the given text in simple bullet points'
                ],
                [
                    'role' => 'user',
                    'content' => $request->input('text')
                ]
            ],
            'temperature' => 0.8,
            'top_p' => 0.1,
            'max_tokens' => 2048,
        ]);

        if ($response->status() !== 200) {
            return response()->json([
                'error' => 'AI request failed',
                'details' => $response->body()
            ], 500);
        }

        $data = $response->json();

        return response()->json([
            'result' => $data['choices'][0]['message']['content']
        ]);
    }
}
