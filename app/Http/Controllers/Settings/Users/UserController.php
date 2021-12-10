<?php

namespace App\Http\Controllers\Settings\Users;

use Inertia\Inertia;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Settings\Users\ViewHelpers\UserIndexViewHelper;

class UserController extends Controller
{
    /**
     * Show the users page.
     *
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Settings/Users/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => UserIndexViewHelper::data(Auth::user()->account),
        ]);
    }
}
