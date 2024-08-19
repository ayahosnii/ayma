<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateStoryRequest;
use App\Models\Story;
use App\Models\StorySlide;
use App\Models\StorySound;
use Illuminate\Http\Request;

class StoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all stories with their slides and sounds
        $stories = Story::with('level')->get();

        return response()->json([
            'stories' => $stories
        ], 200);
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
    public function store(CreateStoryRequest $request)
    {
        // Validate request inputs
        $validatedData = $request->validated();

        // Create a new story
        $story = Story::create([
            'title' => $request->title,
            'level_id' =>  $request->level_id,
        ]);


        // Add slides to the story
        foreach ($request->slides as $index => $slide) {
            if ($request->hasFile("slides.$index.image")) {
                $imagePath = $request->file("slides.$index.image")->store('slides', 'public');
                StorySlide::create([
                    'image' => $imagePath,
                    'context' => $request->input("slides.$index.context"),
                    'story_id' => $story->id,
                ]);
            }
        }

        // Add sounds to the story
        foreach ($request->sounds as $index => $sound) {
            if ($request->hasFile("sounds.$index.sound")) {
                $soundPath = $request->file("sounds.$index.sound")->store('sounds', 'public');
                StorySound::create([
                    'sound' => $soundPath,
                    'story_id' => $story->id,
                ]);
            }
        }

        return response()->json([
            'message' => 'Story created successfully.',
            'story' => $story,
            'slides' => $story->slides,
            'sounds' => $story->sounds
        ], 201);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $story = Story::with(['slides', 'sounds'])->findOrFail($id);

        return response()->json([
            'story' => $story
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'level_id' => 'required|exists:levels,id',
        ]);

        // Find story and update its details
        $story = Story::findOrFail($id);
        $story->update($validatedData);

        return response()->json([
            'message' => 'Story updated successfully',
            'story' => $story
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
{
    $story = Story::findOrFail($id);

    // Delete associated slides and their images
    foreach ($story->slides as $slide) {
        \Storage::disk('public')->delete($slide->image);
        $slide->delete();
    }

    // Delete associated sounds and their files
    foreach ($story->sounds as $sound) {
        \Storage::disk('public')->delete($sound->sound);
        $sound->delete();
    }

    // Delete the story
    $story->delete();

    return response()->json(['message' => 'Story and its associated slides and sounds deleted successfully'], 200);
}

    public function count()
    {
        $count = Story::count();
        return response()->json(['count' => $count]);
    }

    public function getStorySounds($storyId)
    {
        $story = Story::with('sounds')->find($storyId);

        if (!$story) {
            return response()->json(['message' => 'Story not found'], 404);
        }

        return response()->json($story->sounds);
    }

    public function getStorySlides($storyId)
    {
        $story = Story::with('slides')->find($storyId);

        if (!$story) {
            return response()->json(['message' => 'Story not found'], 404);
        }

        return response()->json($story->slides);
    }


    public function destroySound($storyId, $soundId)
{
    $story = Story::find($storyId);

    if (!$story) {
        return response()->json(['message' => 'Story not found'], 404);
    }

    $sound = StorySound::where('story_id', $storyId)->find($soundId);

    if (!$sound) {
        return response()->json(['message' => 'Sound not found'], 404);
    }

    // Delete the sound file from storage
    \Storage::disk('public')->delete($sound->sound);

    // Delete the sound record from the database
    $sound->delete();

    return response()->json(['message' => 'Sound deleted successfully'], 200);
}

public function addSlide(Request $request, $storyId)
{
    $story = Story::findOrFail($storyId);

    $validatedData = $request->validate([
        'context' => 'required|string',
        'image' => 'required|image'
    ]);

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('slides', 'public');
        $slide = StorySlide::create([
            'image' => $imagePath,
            'context' => $validatedData['context'],
            'story_id' => $story->id,
        ]);

        return response()->json([
            'message' => 'Slide added successfully.',
            'slide' => $slide
        ], 201);
    }

    return response()->json(['message' => 'Image is required'], 400);
}


public function updateSlide(Request $request, $storyId, $slideId)
    {
        $story = Story::findOrFail($storyId);
        $slide = StorySlide::where('story_id', $storyId)->findOrFail($slideId);

        $validatedData = $request->validate([
            'context' => 'required|string',
            'image' => 'image|nullable',
        ]);

        if ($request->hasFile('image')) {
            // Delete the old image
            \Storage::disk('public')->delete($slide->image);

            // Store the new image
            $imagePath = $request->file('image')->store('slides', 'public');
            $slide->image = $imagePath;
        }

        $slide->context = $validatedData['context'];
        $slide->save();

        return response()->json(['message' => 'Slide updated successfully.', 'slide' => $slide], 200);
    }

    public function destroySlide($storyId, $slideId)
{
    $story = Story::find($storyId);

    if (!$story) {
        return response()->json(['message' => 'Story not found'], 404);
    }

    $slide = StorySlide::where('story_id', $storyId)->find($slideId);

    if (!$slide) {
        return response()->json(['message' => 'Slide not found'], 404);
    }

    // Delete the slide image from storage
    \Storage::disk('public')->delete($slide->image);

    // Delete the slide record from the database
    $slide->delete();

    return response()->json(['message' => 'Slide deleted successfully'], 200);
}

public function addSound(Request $request, $storyId)
{
    $story = Story::findOrFail($storyId);

    $validatedData = $request->validate([
        'sound' => 'required|mimes:audio/mpeg,mpga,mp3,wav,aac'
    ]);

    if ($request->hasFile('sound')) {
        $soundPath = $request->file('sound')->store('sounds', 'public');
        $sound = StorySound::create([
            'sound' => $soundPath,
            'story_id' => $story->id,
        ]);

        return response()->json([
            'message' => 'Sound added successfully.',
            'sound' => $sound
        ], 201);
    }

    return response()->json(['message' => 'Sound file is required'], 400);
}


}
