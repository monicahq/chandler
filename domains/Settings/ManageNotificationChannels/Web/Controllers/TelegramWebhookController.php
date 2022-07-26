<?php

namespace App\Settings\ManageNotificationChannels\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserNotificationChannel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TelegramWebhookController extends Controller
{
    /**
     * Store Telegram Chat ID from telegram webhook message.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $messageText = $request->message['text'];
        } catch (Exception $e) {
            return response()->json([
                'code'     => $e->getCode(),
                'message'  => 'Accepted with error: \'' . $e->getMessage() . '\'',
            ], 202);
        }

        // check if the message matches the expected pattern.
        // if the message does not match the pattern, then we return a 202 response
        // so telegram will stop trying to send the message.
        if (! Str::of($messageText)->test('/^\/start\s[A-Za-z0-9-]{36}$/')) {
            return response('Accepted', 202);
        }

        // Cleanup the string
        $verificationKey = Str::of($messageText)->remove('/start ')->rtrim();

        // Get Telegram ID from the request.
        $chatId = $request->message['chat']['id'];

        // Get the User ID from the cache using the temp code as key.
        $channel = UserNotificationChannel::where('verification_token', $verificationKey)->first();

        // Update user with the Telegram Chat ID
        $channel->content = $chatId;
        $channel->active = true;
        $channel->save();

        return response('Success', 200);
    }
}
