<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\GrammarOptionController;
use App\Http\Controllers\Api\GrammarQuestionController;
use App\Http\Controllers\Api\LevelsController;
use App\Http\Controllers\Api\OpenAIController;
use App\Http\Controllers\Api\SpeakingController;
use App\Http\Controllers\Api\StoriesController;
use App\Http\Controllers\Api\GrammarGamesController;
use App\Http\Controllers\Api\ListeningController;
use App\Http\Controllers\Api\ListeningQuestionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api', 'prefix' => 'v1'], function () {
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::resource('/categories', CategoryController::class);
    Route::get('/count-categories', [CategoryController::class, 'count']); //Levels Count

    Route::resource('/levels', LevelsController::class);
    Route::get('/count-levels', [LevelsController::class, 'countLevels']); //Levels Count
    Route::get('getAllLevels', [LevelsController::class, 'getAllLevels']); //Levels

    Route::resource('/grammar-games', GrammarGamesController::class); // Grammar Games


    Route::resource('/stories', StoriesController::class);
    Route::get('/count-stories', [StoriesController::class, 'count']); //Stories Count
    Route::get('/stories/{id}/sounds', [StoriesController::class, 'getStorySounds']);     // Sounds table for each story
    Route::delete('stories/{storyId}/sounds/{soundId}', [StoriesController::class, 'destroySound']); // Delete Sound
    Route::post('stories/{story}/sounds', [StoriesController::class, 'addSound']); // Add Sound
    Route::get('/stories/{id}/slides', [StoriesController::class, 'getStorySlides']); // Slides
    Route::post('stories/{storyId}/slides', [StoriesController::class, 'addSlide']); // Add Slide
    Route::post('/stories/{storyId}/slides/{slideId}', [StoriesController::class, 'updateSlide']); // Edit Slide
    Route::delete('stories/{storyId}/slides/{slideId}', [StoriesController::class, 'destroySlide']); //Delete Slide


    Route::resource('grammar-questions', GrammarQuestionController::class)->except(['create']);
    Route::get('get-grammar-games', [GrammarQuestionController::class, 'getGrammarsGames']);
    Route::resource('grammar-options', GrammarOptionController::class)->except(['create', 'edit']);

    Route::post('/speaking', [SpeakingController::class, 'send']);
    Route::post('/generate-response', [OpenAIController::class, 'generateResponse']);

    Route::resource('/listening', ListeningController::class); // Use 'listening' here
    Route::get('/listening/count', [ListeningController::class, 'countListening']); // Update the route

    Route::post('/correct-grammar', function (Request $request) {
        try {
            // Get the sentence from the request
            $sentence = $request->input('sentence');

            if (!$sentence) {
                return response()->json(['error' => 'No sentence provided.'], Response::HTTP_BAD_REQUEST);
            }

            // Escape the sentence to prevent command injection
            $escapedSentence = escapeshellarg($sentence);

            // Path to the Python executable
            $pythonPath = 'python3';

            // Path to the Python script
            $scriptPath = '/var/www/html/python-scripts/correct_grammar.py';

            // Command to execute the Python script
            $command = "\"$pythonPath\" \"$scriptPath\" $escapedSentence";

            // Execute the command
            $corrected = shell_exec($command);

            if ($corrected === null) {
                throw new Exception('Failed to execute the Python script.');
            }

            // Return the corrected sentence as a JSON response
            return response()->json(['corrected_sentence' => trim($corrected)]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    });
});
