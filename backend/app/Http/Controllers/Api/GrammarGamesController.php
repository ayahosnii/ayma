<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateGrammarGameRequest;
use App\Models\GrammarGame;
use Illuminate\Http\Request;

class GrammarGamesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Paginate Grammar Games
        $grammarGames = GrammarGame::with('level')->paginate(3);

        // Return a response
        return response()->json($grammarGames, 200);
    }

    public function getAllGrammarGames()
    {
        // Retrieve all Grammar Games
        $grammarGames = GrammarGame::all();

        // Return a response
        return response()->json($grammarGames);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateGrammarGameRequest $request)
    {
        // Validate the request data
        $validatedData = $request->validated();

        // Create a new Grammar Game
        $grammarGame = GrammarGame::create([
            'title' => $validatedData['title'],
            'level_id' => $validatedData['level_id'],
        ]);

        // Return a response
        return response()->json(['message' => 'Grammar Game created successfully', 'data' => $grammarGame], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Find the Grammar Game by id
        $grammarGame = GrammarGame::findOrFail($id);

        // Return a response
        return response()->json(['data' => $grammarGame], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'level_id' => 'required|integer',
        ]);

        // Find the Grammar Game by id
        $grammarGame = GrammarGame::findOrFail($id);

        // Update the Grammar Game
        $grammarGame->update([
            'title' => $validatedData['title'],
            'level_id' => $validatedData['level_id'],
        ]);

        // Return a response
        return response()->json(['message' => 'Grammar Game updated successfully', 'data' => $grammarGame], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Find the Grammar Game by id
        $grammarGame = GrammarGame::findOrFail($id);

        // Delete the Grammar Game
        $grammarGame->delete();

        // Return a response
        return response()->json(['message' => 'Grammar Game deleted successfully'], 200);
    }

    /**
     * Get the count of Grammar Games
     *
     * @return \Illuminate\Http\Response
     */
    public function countGrammarGames()
    {
        $countGrammarGames = GrammarGame::count();
        return response()->json(['countGrammarGames' => $countGrammarGames]);
    }
}
