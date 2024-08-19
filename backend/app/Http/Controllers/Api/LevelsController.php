<?php

namespace App\Http\Controllers\Api;

use App\Events\LevelCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLevelRequest;
use App\Models\Level;
use Illuminate\Http\Request;

class LevelsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $levels = Level::paginate(3);
        return response()->json($levels, 200);
    }

    public function getAllLevels()
    {
        $levels = Level::all();
        return response()->json($levels);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateLevelRequest $request)
    {
                // Validate the incoming request using the CreateCategoryRequest class

        // Create the category
        $levels = Level::create([
            'name' => $request->name,
            'description' => $request->description,
            // Add other fields as needed
        ]);

        // Dispatch event for push notification
        event(new LevelCreated($levels));

        // Return a success response
        return response()->json(['message' => 'Level created successfully', 'levels' => $levels], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            // Add other fields as needed
        ]);

        // Find the level by id
        $level = Level::findOrFail($id);

        // Update the level's attributes
        $level->name = $request->name;
        $level->description = $request->description;
        // Add other fields as needed

        // Save the updated level
        $level->save();

        // Return a success response
        return response()->json(['message' => 'Level updated successfully', 'level' => $level], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Find the level by id
        $level = Level::findOrFail($id);

        // Delete the level
        $level->delete();

        // Return a success response
        return response()->json(['message' => 'Level deleted successfully'], 200);
    }

    public function countLevels()
    {
        $countLevels = Level::count();
        return response()->json(['countLevels' => $countLevels]);
    }
}
