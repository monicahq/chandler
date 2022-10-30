<?php

namespace App\Settings\ManageReligion\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Settings\ManageGiftOccasions\Web\ViewHelpers\PersonalizeGiftOccasionViewHelper;
use App\Settings\ManageReligion\Services\UpdateReligionPosition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalizeReligionsPositionController extends Controller
{
    public function update(Request $request, int $religionId): JsonResponse
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'religion_id' => $religionId,
            'new_position' => $request->input('position'),
        ];

        $religion = (new UpdateReligionPosition())->execute($data);

        return response()->json([
            'data' => PersonalizeGiftOccasionViewHelper::dto($religion),
        ], 200);
    }
}
