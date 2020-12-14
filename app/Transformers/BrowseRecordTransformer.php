<?php

namespace App\Transformers;

use App\Models\BrowseRecord;
use App\Models\Category;
use League\Fractal\TransformerAbstract;

class BrowseRecordTransformer extends TransformerAbstract
{
    public function transform(BrowseRecord $browseRecord)
    {
        return [
            'id' => $browseRecord->id,
            'type' => $browseRecord->type,
            'times' => $browseRecord->times,
            'created_at' => $browseRecord->created_at->toDateTimeString(),
            'data' => $browseRecord->getData(),
        ];
    }
}
