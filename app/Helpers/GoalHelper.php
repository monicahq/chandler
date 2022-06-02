<?php

namespace App\Helpers;

use App\Models\Goal;
use App\Models\User;
use App\Models\Vault;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GoalHelper
{
    /**
     * Get the information about the contact linked to the given user, in the
     * given vault.
     *
     * @return null|array
     */
    public static function getStreakData(Goal $goal): ?array
    {
        $streaks = $goal->streaks()
            ->orderBy('happened_at')
            ->pluck('happened_at')
            ->toArray();

        $maxStreaks = 0;
        $currentStreak = 0;
        for ($i = 0; $i < count($streaks) - 1; $i++) {
            $currentEntry = Carbon::parse($streaks[$i]);
            $nextEntry = Carbon::parse($streaks[$i + 1] ?? null);

            Log::info('Current entry: ' . $currentEntry->toDateString());
            Log::info('Next entry: ' . $nextEntry->toDateString());
            Log::info($currentEntry->copy()->addDay()->toDateString());
            if ($currentEntry->copy()->addDay()->isSameDay($nextEntry)) {
                $currentStreak++;

                if ($currentStreak > $maxStreaks) {
                    $maxStreaks = $currentStreak;
                }
            } else {
                $currentStreak = 0;
            }
        }

        return [
            'max_streak' => $maxStreaks + 1,
            'current_streak' => $currentStreak + 1,
        ];
    }
}
