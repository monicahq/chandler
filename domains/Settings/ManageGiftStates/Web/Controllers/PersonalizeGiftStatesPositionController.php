<?php

namespace App\Settings\ManageGiftStates\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Settings\ManageGiftStates\Services\UpdateGiftStatePosition;
use App\Settings\ManageGiftStates\Web\ViewHelpers\PersonalizeGiftStateViewHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalizeGiftStatesPositionController extends Controller
{
    public function update(Request $request, int $giftStageId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'gift_state_id' => $giftStageId,
            'new_position' => $request->input('position'),
        ];

        $giftStage = (new UpdateGiftStatePosition)->execute($data);

        return response()->json([
            'data' => PersonalizeGiftStateViewHelper::dto($giftStage),
        ], 200);
    }
}
