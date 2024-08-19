<?php

namespace Modules\GrammarGames\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QuestionResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'options' => OptionResource::collection($this->options),
        ];
    }
}
