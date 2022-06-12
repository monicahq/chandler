<?php

namespace App\Settings\ManageGroupTypes\Web\Controllers;

use App\Contact\ManageGroups\Services\CreateGroup;
use App\Contact\ManageGroups\Services\DestroyGroup;
use App\Http\Controllers\Controller;
use App\Settings\ManageGiftStates\Services\CreateGiftState;
use App\Settings\ManageGiftStates\Services\DestroyGiftState;
use App\Settings\ManageGiftStates\Services\UpdateGiftState;
use App\Settings\ManageGroupTypes\Services\CreateGroupType;
use App\Settings\ManageGroupTypes\Services\CreateGroupTypeRole;
use App\Settings\ManageGroupTypes\Services\DestroyGroupType;
use App\Settings\ManageGroupTypes\Services\DestroyGroupTypeRole;
use App\Settings\ManageGroupTypes\Services\UpdateGroupType;
use App\Settings\ManageGroupTypes\Services\UpdateGroupTypeRole;
use App\Settings\ManageGroupTypes\Web\ViewHelpers\PersonalizeGroupTypeViewHelper;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PersonalizeGroupTypeRoleController extends Controller
{
    public function store(Request $request, int $groupTypeId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'group_type_id' => $groupTypeId,
            'label' => $request->input('label'),
        ];

        $groupTypeRole = (new CreateGroupTypeRole)->execute($data);

        return response()->json([
            'data' => PersonalizeGroupTypeViewHelper::dtoGroupTypeRole($groupTypeRole),
        ], 201);
    }

    public function update(Request $request, int $groupTypeId, int $groupTypeRoleId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'group_type_id' => $groupTypeId,
            'group_type_role_id' => $groupTypeRoleId,
            'label' => $request->input('label'),
        ];

        $groupTypeRole = (new UpdateGroupTypeRole)->execute($data);

        return response()->json([
            'data' => PersonalizeGroupTypeViewHelper::dtoGroupTypeRole($groupTypeRole),
        ], 200);
    }

    public function destroy(Request $request, int$groupTypeId, int $groupTypeRoleId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'group_type_id' => $groupTypeId,
            'group_type_role_id' => $groupTypeRoleId,
        ];

        (new DestroyGroupTypeRole)->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
