<?php

namespace App\Http\Controllers\Settings\Notifications\ViewHelpers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Contact;
use App\Helpers\NameHelper;
use App\Models\UserNotificationChannel;

class NotificationsIndexViewHelper
{
    public static function data(User $user): array
    {
        $channels = $user->notificationChannels;

        // emails
        $emails = $channels->filter(function ($channel) {
            return $channel->type === 'email';
        });
        $emailsCollection = $emails->map(function ($channel) use ($user) {
            return self::dtoEmail($channel, $user);
        });

        return [
            'emails' => $emailsCollection,
            'url' => [
                'settings' => route('settings.index'),
                'back' => route('settings.index'),
            ],
        ];
    }

    public static function dtoEmail(UserNotificationChannel $channel, User $user): array
    {
        return [
            'id' => $channel->id,
            'type' => $channel->type,
            'label' => $channel->label,
            'content' => $channel->content,
            'active' => $channel->active,
            'verified_at' => $channel->verified_at ? $channel->verified_at->format('Y-m-d H:i:s') : null,
            'url' => [
                'store' => route('settings.notifications.store'),
            ],
        ];
    }
}
