<?php

namespace App\Http\Controllers\Settings\CancelAccount;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Account\ManageUsers\InviteUser;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Settings\CancelAccount\ViewHelpers\CancelAccountIndexViewHelper;

class CancelAccountController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/CancelAccount/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => CancelAccountIndexViewHelper::data(),
        ]);
    }

    public function destroy(Request $request)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'email' => $request->input('email'),
        ];

        (new InviteUser)->execute($data);

        return response()->json([
            'data' => route('settings.user.index'),
        ], 201);
    }
}
