<?php

namespace App\Settings\ManageGiftOccasions\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Settings\ManageGiftOccasions\Services\UpdateGiftOccasionPosition;
use App\Settings\ManageLifeEventCategories\Services\UpdateLifeEventTypePosition;
use App\Settings\ManageLifeEventCategories\Web\ViewHelpers\PersonalizeLifeEventCategoriesViewHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalizeGiftOccasionsPositionController extends Controller
{
    public function update(Request $request, int $giftOccasionId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'gift_occasion_id' => $giftOccasionId,
            'new_position' => $request->input('position'),
        ];

        $giftOccasion = (new UpdateGiftOccasionPosition)->execute($data);

        return response()->json([
            'data' => PersonalizeLifeEventCategoriesViewHelper::dtoType($giftOccasion->lifeEventCategory, $lifeEventType),
        ], 200);
    }
}
