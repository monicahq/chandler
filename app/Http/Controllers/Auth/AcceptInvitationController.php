<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AcceptInvitationController extends Controller
{
    public function show(Request $request, string $code)
    {
        return Inertia::render('Auth/AcceptInvitation', [
        ]);
    }
}
