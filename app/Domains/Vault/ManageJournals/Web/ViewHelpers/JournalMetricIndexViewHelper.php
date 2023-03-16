<?php

namespace App\Domains\Vault\ManageJournals\Web\ViewHelpers;

use App\Helpers\SliceOfLifeHelper;
use App\Models\Journal;
use App\Models\JournalMetric;
use App\Models\SliceOfLife;

class JournalMetricIndexViewHelper
{
    public static function data(Journal $journal): array
    {
        $slices = $journal->journalMetrics()
            ->orderBy('label')
            ->get()
            ->map(fn (JournalMetric $journalMetric) => self::dto($journalMetric));

        return [
            'journal' => [
                'id' => $journal->id,
                'name' => $journal->name,
                'url' => [
                    'show' => route('journal.show', [
                        'vault' => $journal->vault_id,
                        'journal' => $journal->id,
                    ]),
                ],
            ],
            'slicesOfLife' => $slices,
            'url' => [
                'store' => route('slices.store', [
                    'vault' => $journal->vault_id,
                    'journal' => $journal->id,
                ]),
            ],
        ];
    }

    public static function dto(JournalMetric $metric): array
    {
        return [
            'id' => $metric->id,
            'label' => $metric->label,
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
