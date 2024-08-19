<?php

namespace Modules\GrammarGames\Http\Controllers;

use App\Models\GrammarGame;
use App\Models\GrammarQuestion;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Modules\GrammarGames\Services\QuestionServiceInterface;
use Modules\GrammarGames\Transformers\QuestionResource;

class QuestionController extends Controller
{
    protected $questionService;

    public function __construct(QuestionServiceInterface $questionService)
    {
        $this->questionService = $questionService;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('grammargames::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('grammargames::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request): JsonResponse
    {
        // Retrieve the current question count from the session
        $count = Session::get('question_count', 1);

        // If it's the first question, only validate the grammar game ID
        if ($count === 1) {
            $request->validate([
                'grammar_game_id' => 'required|exists:grammar_games,id',
            ]);

            // Increment the question count and store it in the session
            $count++;
            Session::put('question_count', $count);

            // Get the question for the grammar game and return it
            $question = $this->questionService->getRandomQuestion($request->grammar_game_id);
            // Store the correct answer for the current question in the session
            Session::put('previous_question_answer', $question->correct_answer);
            return response()->json([
                'question' => $question,
            ]);
        }

        // For subsequent questions, validate the answer along with the grammar game ID
        $request->validate([
            'grammar_game_id' => 'required|exists:grammar_games,id',
            'answer' => 'required|string',
        ]);

        // Get the correct answer for the previous question from the session
        $previousQuestionAnswer = Session::get('previous_question_answer');

        // Get the user's answer for the current question
        $userAnswer = $request->input('answer');

        // Compare the user's answer with the correct answer for the previous question
        $isCorrect = $userAnswer === $previousQuestionAnswer;

        // If the user's answer is correct, increment the question count and store it in the session
        if ($isCorrect) {
            $count++;
            Session::put('question_count', $count);
        }

        // Get the next question for the grammar game and return it
        $nextQuestion = $this->questionService->getRandomQuestion($request->grammar_game_id);

        // Store the correct answer for the current question in the session
        Session::put('previous_question_answer', $nextQuestion->correct_answer);

        // Return the result and the next question
        return response()->json([
            'is_correct' => $isCorrect,
            'next_question' => $nextQuestion,
        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('grammargames::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('grammargames::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
