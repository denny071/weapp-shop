<?php

namespace App\Transformers;

use App\Models\Category;
use App\Models\SearchWord;
use League\Fractal\TransformerAbstract;

class SearchWordTransformer extends TransformerAbstract
{
    public function transform(SearchWord $searchWord)
    {
        return [
            'word' => $searchWord->word,
            'times' => $searchWord->times
        ];
    }
}
