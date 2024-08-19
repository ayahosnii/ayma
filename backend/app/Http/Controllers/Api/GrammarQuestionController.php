<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGrammarQuestionRequest;
use App\Http\Requests\UpdateGrammarQuestionRequest;
use App\Models\GrammarGame;
use App\Models\GrammarQuestion;
use App\Services\JsonResponseHandler;
use Illuminate\Http\Request;

class GrammarQuestionController extends Controller
{
    public function index()
    {
        $questions = GrammarQuestion::with('options')->get();
        return JsonResponseHandler::getInstance()->success($questions);
    }

    public function store(StoreGrammarQuestionRequest $request)
    {
        $question = GrammarQuestion::create([
            'text' => $request->text,
            'grammar_game_id' => $request->gameId,
        ]);

        foreach ($request->answer_options as $option) {
            $question->options()->create($option);
        }

        return JsonResponseHandler::getInstance()->success($question->load('options'), 201);
    }

    public function show($id)
    {
        $question = GrammarQuestion::with('options')->findOrFail($id);
        return JsonResponseHandler::getInstance()->success($question);
    }

    public function edit($id)
    {
        $questions = GrammarQuestion::with('options', 'game')->where('id', $id)->first();
        return JsonResponseHandler::getInstance()->success($questions);
    }

    public function update(UpdateGrammarQuestionRequest $request, $id)
    {
        $question = GrammarQuestion::findOrFail($id);

        if ($request->has('text')) {
            $question->update(['text' => $request->text]);
        }

        if ($request->has('answer_options')) {
            $question->options()->delete();
            foreach ($request->answer_options as $option) {
                $question->options()->create($option);
            }
        }

        return JsonResponseHandler::getInstance()->success($question->load('options'));
    }

    public function destroy($id)
    {
        $question = GrammarQuestion::findOrFail($id);
        $question->delete();

        return JsonResponseHandler::getInstance()->success(null, 204, 'Deleted successfully');
    }

    public function getGrammarsGames()
    {
        $grammarGames = GrammarGame::get();
        return response()->json($grammarGames);
    }
}
