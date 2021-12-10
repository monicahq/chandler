<?php

namespace App\Http\Controllers\Settings\Users;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Settings\Users\ViewHelpers\UserIndexViewHelper;
use App\Http\Controllers\Settings\Users\ViewHelpers\UserCreateViewHelper;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Users/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => UserIndexViewHelper::data(Auth::user()->account),
        ]);
    }

    public function create()
    {
        return Inertia::render('Settings/Users/Create', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => UserCreateViewHelper::data(Auth::user()->account),
        ]);
    }
}
