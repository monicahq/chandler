<?php

namespace App\Domains\Settings\ExportAccount\Jobs;

use App\Domains\Settings\ExportAccount\Services\JsonExportAccount;
use App\Helpers\StorageHelper;
use App\Models\ExportJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\File;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ExportAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Path to the export file.
     */
    protected string $path = '';

    /**
     * Export job to run.
     */
    protected ExportJob $exportJob;

    /**
     * Create a new job instance.
     */
    public function __construct(ExportJob $exportJob, string $path = null)
    {
        $exportJob->status = ExportJob::EXPORT_TODO;
        $exportJob->save();
        $this->exportJob = $exportJob->withoutRelations();
        $this->path = $path ?? "exports/{$exportJob->account->id}";
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->exportJob->start();

        $tempFileName = '';
        try {
            $tempFileName = app(JsonExportAccount::class)->execute([
                'account_id' => $this->exportJob->account_id,
                'author_id' => $this->exportJob->author_id,
            ]);

            // get the temp file that we just created
            $tempFilePath = StorageHelper::disk('local')->path($tempFileName);

            // move the file to the public storage
            $file = StorageHelper::disk(config('filesystems.default'))
                ->putFileAs($this->path, new File($tempFilePath), basename($tempFileName));

            $this->exportJob->location = config('filesystems.default');
            $this->exportJob->filename = $file;

            $this->exportJob->end();
        } catch (Throwable $e) {
            $this->fail($e);
        } finally {
            // delete old file from temp folder
            $storage = Storage::disk('local');
            if ($storage->exists($tempFileName)) {
                $storage->delete($tempFileName);
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        $this->exportJob->status = ExportJob::EXPORT_FAILED;
        $this->exportJob->save();
    }
}
