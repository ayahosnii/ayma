<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGrammarQuestionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'text' => ['required', 'string'],
            'answer_options' => ['required', 'array'],
            'answer_options.*.option' => ['required', 'string'],
            'answer_options.*.is_correct' => ['required', 'boolean'],
        ];
    }
}
