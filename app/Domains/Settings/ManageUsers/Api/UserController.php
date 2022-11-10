<?php

namespace App\Domains\Settings\ManageUsers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\RouteAttributes\Attributes\{Get, Middleware, Prefix};

#[Middleware('api')]
#[Prefix('api')]
class UserController extends Controller
{
    #[Get('user', middleware: 'abilities:read')]
    public function __invoke(Request $request)
    {
        return $request->user();
    }
}
