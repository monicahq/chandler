<?php

namespace App\Http\Controllers\Settings;

use Inertia\Inertia;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\ViewHelpers\UserIndexViewHelper;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Show the users page.
     *
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Settings/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => UserIndexViewHelper::data(Auth::user()->account),
        ]);
    }
}
