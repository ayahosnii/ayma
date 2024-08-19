<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGrammarQuestionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'text' => 'sometimes|required|string',
            'answer_options' => 'sometimes|required|array',
            'answer_options.*.option' => 'required_with:answer_options|string',
            'answer_options.*.is_correct' => 'required_with:answer_options|boolean',
        ];
    }
}
