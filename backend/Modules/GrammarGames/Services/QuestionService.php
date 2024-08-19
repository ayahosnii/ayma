<?php

namespace Modules\GrammarGames\Services;

use App\Models\GrammarQuestion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class QuestionService implements QuestionServiceInterface
{
    public function getRandomQuestion($gameId)
    {
        $answeredQuestionIds = [];
        // Get all question IDs from the database
        $allQuestionIds = GrammarQuestion::where('grammar_game_id', $gameId)->pluck('id')->toArray();

        // Exclude answered question IDs from all question IDs
        $remainingQuestionIds = array_diff($allQuestionIds, $answeredQuestionIds);

        // Check if there are remaining questions to be answered
        if (empty($remainingQuestionIds)) {
            return null; // All questions answered
        }

        // Generate a cache key based on remaining question IDs
        $cacheKey = 'random_question_' . implode('_', $remainingQuestionIds);

        return Cache::remember($cacheKey, now()->addMinute(), function () use ($remainingQuestionIds) {
            // Fetch a random question from the remaining question IDs
            return GrammarQuestion::with('options')->whereIn('id', $remainingQuestionIds)->inRandomOrder()->first();
        });
    }
}

