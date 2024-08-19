<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateListeningRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Set this to false if you want to implement authorization logic
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'audio' => 'required|file|mimes:audio/mpeg,mpga,mp3,wav',
            'transcript' => 'nullable|string',
        ];
    }
}
