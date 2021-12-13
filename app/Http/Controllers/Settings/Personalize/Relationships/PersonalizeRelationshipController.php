<?php

namespace App\Http\Controllers\Settings\Personalize\Relationships;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Personalize\Relationships\ViewHelpers\PersonalizeRelationshipIndexViewHelper;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Settings\Personalize\ViewHelpers\PersonalizeIndexViewHelper;

class PersonalizeRelationshipController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Personalize/Relationships/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => PersonalizeRelationshipIndexViewHelper::data(Auth::user()->account),
        ]);
    }
}
