<?php

namespace App\Domains\Vault\ManageJournals\Web\ViewHelpers;

use App\Models\SliceOfLife;

class SliceOfLifeShowViewHelper
{
    public static function data(SliceOfLife $slice): array
    {
        return [
            'journal' => [
                'id' => $slice->journal->id,
                'name' => $slice->journal->name,
                'url' => [
                    'show' => route('journal.show', [
                        'vault' => $slice->journal->vault_id,
                        'journal' => $slice->journal->id,
                    ]),
                ],
            ],
            'slice' => [
                'id' => $slice->id,
                'name' => $slice->name,
                'url' => [
                    'show' => route('slices.show', [
                        'vault' => $slice->journal->vault_id,
                        'journal' => $slice->journal_id,
                        'slice' => $slice->id,
                    ]),
                ],
            ],
        ];
    }

    public static function dtoSlice(SliceOfLife $slice): array
    {
        return [
            'id' => $slice->id,
            'name' => $slice->name,
            'url' => [
                'show' => route('slices.show', [
                    'vault' => $slice->journal->vault_id,
                    'journal' => $slice->journal_id,
                    'slice' => $slice->id,
                ]),
            ],
        ];
    }
}
