<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GrammarOption;
use App\Services\JsonResponseHandler;
use Illuminate\Http\Request;

class GrammarOptionController extends Controller
{
    public function index()
    {
        $options = GrammarOption::all();
        return JsonResponseHandler::getInstance()->success($options);
    }

    public function store(Request $request)
    {
        $request->validate([
            'grammar_question_id' => 'required|exists:grammar_questions,id',
            'option' => 'required|string',
            'is_correct' => 'required|boolean',
        ]);

        $option = GrammarOption::create($request->all());

        return JsonResponseHandler::getInstance()->success($option, 201);
    }

    public function show($id)
    {
        $option = GrammarOption::findOrFail($id);
        return JsonResponseHandler::getInstance()->success($option);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'option' => 'sometimes|required|string',
            'is_correct' => 'sometimes|required|boolean',
        ]);

        $option = GrammarOption::findOrFail($id);
        $option->update($request->all());

        return JsonResponseHandler::getInstance()->success($option);
    }

    public function destroy($id)
    {
        $option = GrammarOption::findOrFail($id);
        $option->delete();

        return JsonResponseHandler::getInstance()->success(null, 204, 'Deleted successfully');
    }
}
