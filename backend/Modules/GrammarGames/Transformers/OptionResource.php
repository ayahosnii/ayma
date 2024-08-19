<?php

namespace Modules\GrammarGames\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OptionResource extends ResourceCollection
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
            'option' => $this->option,
            'is_correct' => $this->is_correct,
        ];
    }
}
