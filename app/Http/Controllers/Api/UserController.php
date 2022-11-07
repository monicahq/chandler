<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\RouteAttributes\Attributes\Get;

class UserController extends Controller
{
    #[Get('user', middleware: 'abilities:read')]
    public function __invoke(Request $request)
    {
        return $request->user();
    }
}
