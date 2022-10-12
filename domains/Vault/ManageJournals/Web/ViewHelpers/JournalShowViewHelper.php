<?php

namespace App\Vault\ManageJournals\Web\ViewHelpers;

use App\Helpers\DateHelper;
use App\Helpers\SQLHelper;
use App\Models\Journal;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JournalShowViewHelper
{
    public static function data(Journal $journal, int $year, User $user): array
    {
        $monthsCollection = self::postsInYear($journal, $year, $user);

        return [
            'id' => $journal->id,
            'name' => $journal->name,
            'description' => $journal->description,
            'months' => $monthsCollection,
            'years' => self::yearsOfContentInJournal($journal, $user),
            'url' => [
                'create' => route('post.create', [
                    'vault' => $journal->vault_id,
                    'journal' => $journal->id,
                ]),
            ],
        ];
    }

    /**
     * Get all the posts in the given year, ordered by month descending.
     *
     * @param  Journal  $journal
     * @param  int  $year
     * @param  User  $user
     * @return Collection
     */
    public static function postsInYear(Journal $journal, int $year, User $user): Collection
    {
        $monthsCollection = collect();
        for ($month = 12; $month > 0; $month--) {
            $postsCollection = collect();

            $posts = $journal->posts()
                ->orderBy('written_at', 'desc')
                ->whereYear('written_at', (string) $year)
                ->whereMonth('written_at', (string) $month)
                ->get();

            // if ($posts->count() === 0) {
            //     continue;
            // }

            foreach ($posts as $post) {
                $postsCollection->push([
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => $post->excerpt,
                    'written_at_day' => Str::upper(DateHelper::formatShortDay($post->written_at, $user)),
                    'written_at_day_number' => DateHelper::formatDayNumber($post->written_at),
                    'url' => [
                        'show' => route('post.show', [
                            'vault' => $journal->vault_id,
                            'journal' => $journal->id,
                            'post' => $post->id,
                        ]),
                    ],
                ]);
            }

            $monthsCollection->push([
                'month' => DateHelper::formatLongMonthAndYear($post->written_at),
                'posts' => $postsCollection,
            ]);
        }

        return $monthsCollection;
    }

    /**
     * Get all the years that have posts in the journal.
     *
     * @param  Journal  $journal
     * @return Collection
     */
    public static function yearsOfContentInJournal(Journal $journal): Collection
    {
        $posts = Post::where('journal_id', $journal->id)
            ->select(DB::raw(SQLHelper::year('written_at').' as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->get();

        return $posts->map(fn (Post $post) => [
            'year' => $post->year,
            'posts' => Post::where('journal_id', $journal->id)
                ->whereYear('written_at', $post->year)
                ->count(),
            'url' => [
                'show' => route('journal.year', [
                    'vault' => $journal->vault_id,
                    'journal' => $journal->id,
                    'year' => $post->year,
                ]),
            ],
        ]);
    }
}
