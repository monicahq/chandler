<?php

namespace App\Helpers;

use App\Models\Account;

class SubscriptionHelper
{
    /**
     * Indicates whether the given account has limitations with her current
     * plan.
     *
     * @return bool
     */
    public static function hasLimitations(Account $account): bool
    {
        if (! config('monica.requires_subscription')) {
            return false;
        }

        if (self::hasValidSubscription($account)) {
            return false;
        }

        if (self::hasReachedContactLimit($account)) {
            return true;
        }

        return true;
    }

    /**
     * Indicate whether an account has reached the contact limit if the account
     * is on a free trial.
     *
     * @param  Account  $account
     * @return bool
     */
    public static function hasReachedContactLimit(Account $account): bool
    {
        $vaults = $account->vaults()->with('contacts')->get();
        $contacts = 0;
        foreach ($vaults as $vault) {
            $contacts += $vault->contacts->count();
        }

        return $contacts >= 5;
    }

    public static function hasValidSubscription(Account $account): bool
    {
        if ($account->licence_key === null) {
            return false;
        }

        if ($account->valid_until_at === null || $account->valid_until_at->isPast()) {
            return false;
        }

        return true;
    }
}
