<?php

namespace Modules\GrammarGames\Services;

use App\Models\GrammarQuestion;

interface QuestionServiceInterface
{
    /**
     * Get a random question along with its options.
     *
     * @return GrammarQuestion|null
     */
    public function getRandomQuestion($gameId);
}

