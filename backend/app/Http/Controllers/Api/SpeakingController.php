<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIMLApiService;
use Illuminate\Http\Request;

class SpeakingController extends Controller
{
    protected $aimlApi;

    public function __construct(AIMLApiService $aimlApi)
    {
        $this->aimlApi = $aimlApi;
    }

    public function send(Request $request)
    {
        $prompt = $request->prompt . " Just fix the grammar and return in HTML. Highlight incorrect text in red and provide the corrected grammar in green beside it.";

        $response = $this->aimlApi->generateText($prompt, 'openchat/openchat-3.5-1210', ['text']);

        return response()->json($response);
    }
}
