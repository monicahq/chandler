<?php

namespace App\Domains\Settings\ExportAccount\Web\Controllers;

use App\Domains\Settings\ExportAccount\Jobs\ExportAccount;
use App\Domains\Settings\ExportAccount\Web\ViewHelpers\ExportAccountViewHelper;
use App\Domains\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\ExportJob;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExportAccountController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Settings/ExportAccount/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => ExportAccountViewHelper::data($request->user()->account),
        ]);
    }

    public function store(Request $request)
    {
        $exportJob = $request->user()->account->exportJobs()->create([
            'author_id' => $request->user()->id,
        ]);
        ExportAccount::dispatch($exportJob);

        return $this->index($request);
    }

    /**
     * Download the generated file.
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response|null
     */
    public function download(Request $request, string $id)
    {
        $job = ExportJob::where([
            'account_id' => $request->user()->account_id,
            'author_id' => $request->user()->id,
            'id' => $id,
        ])->firstOrFail();

        if ($job->status !== ExportJob::EXPORT_DONE) {
            return redirect()->route('settings.export.index')
                ->withErrors(__('Download impossible, this export is not done yet.'));
        }
        $disk = StorageHelper::disk($job->location);

        return $disk->response($job->filename,
            "monica.json",
            [
                'Content-Type' => "application/json; charset=utf-8",
                'Content-Disposition' => "attachment; filename=monica.json",
            ]
        );
    }
}
