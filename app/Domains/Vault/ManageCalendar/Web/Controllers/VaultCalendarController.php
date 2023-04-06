<?php

namespace App\Domains\Vault\ManageCalendar\Web\Controllers;

use App\Domains\Vault\ManageCalendar\Web\ViewHelpers\VaultCalendarIndexViewHelper;
use App\Domains\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Controller;
use App\Models\Vault;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class VaultCalendarController extends Controller
{
    public function index(Request $request, int $vaultId)
    {
        $vault = Vault::findOrFail($vaultId);

        return Inertia::render('Vault/Calendar/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => VaultCalendarIndexViewHelper::data(
                vault: $vault,
                user: Auth::user(),
                year: Carbon::now()->year,
                month: Carbon::now()->month,
            ),
        ]);
    }

    public function month(Request $request, int $vaultId, int $year, int $month)
    {
        $vault = Vault::findOrFail($vaultId);

        return Inertia::render('Vault/Calendar/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => VaultCalendarIndexViewHelper::data(
                vault: $vault,
                user: Auth::user(),
                year: $year,
                month: $month,
            ),
        ]);
    }

    public function day(Request $request, int $vaultId, int $year, int $month, int $day)
    {
        $vault = Vault::findOrFail($vaultId);

        return response()->json([
            'data' => VaultCalendarIndexViewHelper::getDay(
                vault: $vault,
                year: $year,
                month: $month,
                day: $day,
            ),
        ], 200);
    }
}
