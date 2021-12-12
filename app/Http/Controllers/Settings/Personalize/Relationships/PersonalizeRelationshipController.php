<?php

namespace App\Http\Controllers\Settings\Personalize\Relationships;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Personalize\ViewHelpers\PersonalizeIndexViewHelper;
use Illuminate\Support\Facades\Auth;
use App\Services\Account\ManageUsers\InviteUser;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Settings\Users\ViewHelpers\UserIndexViewHelper;
use App\Http\Controllers\Settings\Users\ViewHelpers\UserCreateViewHelper;

class PersonalizeRelationshipController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Personalize/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => PersonalizeIndexViewHelper::data(Auth::user()),
        ]);
    }
}
