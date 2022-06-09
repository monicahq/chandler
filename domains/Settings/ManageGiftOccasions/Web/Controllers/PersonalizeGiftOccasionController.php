<?php

namespace App\Settings\ManageGiftOccasions\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Settings\ManageGiftOccasions\Services\CreateGiftOccasion;
use App\Settings\ManageGiftOccasions\Services\DestroyGiftOccasion;
use App\Settings\ManageGiftOccasions\Services\UpdateGiftOccasion;
use App\Settings\ManageGiftOccasions\Web\ViewHelpers\PersonalizeGiftOccasionViewHelper;
use App\Settings\ManageRelationshipTypes\Services\CreateRelationshipType;
use App\Settings\ManageRelationshipTypes\Services\DestroyRelationshipType;
use App\Settings\ManageRelationshipTypes\Services\UpdateRelationshipType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalizeGiftOccasionController extends Controller
{
    public function store(Request $request, int $giftOccasionId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'gift_occasion_id' => $giftOccasionId,
            'label' => $request->input('label'),
        ];

        $giftOccasion = (new CreateGiftOccasion)->execute($data);

        return response()->json([
            'data' => PersonalizeGiftOccasionViewHelper::dto($giftOccasion),
        ], 201);
    }

    public function update(Request $request, int $giftOccasionId, int $giftOccasion)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'gift_occasion_id' => $giftOccasionId,
            'label' => $request->input('label'),
        ];

        $giftOccasion = (new UpdateGiftOccasion)->execute($data);

        return response()->json([
            'data' => PersonalizeGiftOccasionViewHelper::dto($giftOccasion),
        ], 200);
    }

    public function destroy(Request $request, int $giftOccasionId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'gift_occasion_id' => $giftOccasionId,
        ];

        (new DestroyGiftOccasion)->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
