<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OpenAIService;
use Illuminate\Http\Request;

class OpenAIController extends Controller
{
    protected $huggingFaceService;

    public function __construct(OpenAIService $huggingFaceService)
    {
        $this->huggingFaceService = $huggingFaceService;
    }

    public function generateResponse(Request $request)
    {
        $prompt = $request->prompt . " Just fix the grammar and return in HTML. Highlight incorrect text in red and provide the corrected grammar in green beside it.";
        $response = $this->huggingFaceService->generateResponse($prompt);

        return response()->json($response);
    }
}
