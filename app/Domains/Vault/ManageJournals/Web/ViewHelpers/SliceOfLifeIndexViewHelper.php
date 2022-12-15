<?php

namespace App\Domains\Vault\ManageJournals\Web\ViewHelpers;

use App\Models\Journal;
use App\Models\SliceOfLife;

class SliceOfLifeIndexViewHelper
{
    public static function data(Journal $journal): array
    {
        $slices = $journal->slicesOfLife()
            ->orderBy('name')
            ->get()
            ->map(fn (SliceOfLife $slice) => [
                'id' => $slice->id,
                'name' => $slice->name,
            ]);

        return [
            'slicesOfLife' => $slices,
        ];
    }
}
