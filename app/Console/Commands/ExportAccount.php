<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Domains\Settings\ExportAccount\Services\JsonExportAccount;
use App\Helpers\StorageHelper;
use Illuminate\Support\Facades\Storage;

class ExportAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monica:export-account
                            {--user=user : The user id to export.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export an account';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $user = User::findOrFail($this->option('user'));

        $tempFileName = '';
        try {
            $tempFileName = app(JsonExportAccount::class)->execute([
                'account_id' => $user->account_id,
                'author_id' => $user->id,
            ]);

            $file = StorageHelper::disk('local')->get($tempFileName);

            $this->line($file);
        } finally {
            // delete old file from temp folder
            $storage = Storage::disk('local');
            if ($storage->exists($tempFileName)) {
                $storage->delete($tempFileName);
            }
        }
    }
}
