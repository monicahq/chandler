<?php

namespace App\Domains\Vault\ManageJournals\Web\Controllers;

use App\Domains\Vault\ManageJournals\Services\CreateJournalMetric;
use App\Domains\Vault\ManageJournals\Services\CreateSliceOfLife;
use App\Domains\Vault\ManageJournals\Services\DestroySliceOfLife;
use App\Domains\Vault\ManageJournals\Services\UpdateSliceOfLife;
use App\Domains\Vault\ManageJournals\Web\ViewHelpers\JournalMetricIndexViewHelper;
use App\Domains\Vault\ManageJournals\Web\ViewHelpers\SliceOfLifeEditViewHelper;
use App\Domains\Vault\ManageJournals\Web\ViewHelpers\SliceOfLifeIndexViewHelper;
use App\Domains\Vault\ManageJournals\Web\ViewHelpers\SliceOfLifeShowViewHelper;
use App\Domains\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Controller;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Redirect;

class JournalMetricController extends Controller
{
    public function index(Request $request, int $vaultId, int $journalId)
    {
        $vault = Vault::findOrFail($vaultId);
        $journal = $vault->journals()->findOrFail($journalId);

        return Inertia::render('Vault/Journal/Metrics/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => JournalMetricIndexViewHelper::data($journal),
        ]);
    }

    public function store(Request $request, int $vaultId, int $journalId)
    {
        $vault = Vault::findOrFail($vaultId);
        $vault->journals()->findOrFail($journalId);

        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vaultId,
            'journal_id' => $journalId,
            'name' => $request->input('name'),
        ];

        $journalMetric = (new CreateJournalMetric())->execute($data);

        return response()->json([
            'data' => JournalMetricIndexViewHelper::dto($journalMetric),
        ], 201);
    }
}
