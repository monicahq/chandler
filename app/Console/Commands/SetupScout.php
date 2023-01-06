<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @codeCoverageIgnore
 */
class SetupScout extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:setup
                            {--flush : Flush the indexes.}
                            {--import : Import the models into the search index.}
                            {--force : Force the operation to run when in production.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup scout indexes.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if ($this->confirmToProceed()) {
            $this->scoutConfigure();
            $this->scoutFlush();
            $this->scoutImport();
        }
    }

    /**
     * Configure scout.
     *
     * @return void
     */
    protected function scoutConfigure(): void
    {
        if (config('scout.driver') === 'meilisearch' && (config('scout.meilisearch.host')) !== '') {
            $this->artisan('☐ Updating indexes on Meilisearch', 'scout:sync-index-settings', ['--verbose' => true]);
        }
    }

    /**
     * Import models.
     *
     * @return void
     */
    protected function scoutFlush(): void
    {
        if (config('scout.driver') !== null && $this->option('flush')) {
            foreach (config('scout.meilisearch.index-settings') as $index => $settings) {
                $name = (new $index)->getTable();
                $this->artisan("☐ Flush {$name} index", 'scout:flush', ['model' => $index, '--verbose' => true]);
            }

            $this->info('✓ Indexes flushed');
        }
    }

    /**
     * Import models.
     *
     * @return void
     */
    protected function scoutImport(): void
    {
        if (config('scout.driver') !== null && $this->option('import')) {
            foreach (config('scout.meilisearch.index-settings') as $index => $settings) {
                $name = (new $index)->getTable();
                $this->artisan("☐ Import {$name}", 'scout:import', ['model' => $index, '--verbose' => true]);
            }

            $this->info('✓ Indexes imported');
        }
    }

    private function artisan(string $message, string $command, array $options = [])
    {
        $this->info($message);
        $this->getOutput()->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE
            ? $this->call($command, $options)
            : $this->callSilent($command, $options);
    }
}
