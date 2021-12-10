<?php

namespace App\Http\Controllers\Settings\Users;

use Inertia\Inertia;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Users\ViewHelpers\UserCreateViewHelper;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Settings\Users\ViewHelpers\UserIndexViewHelper;

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
