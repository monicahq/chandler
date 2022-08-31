<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\Schema;
use MeiliSearch\Client;
use Symfony\Component\Console\Output\OutputInterface;

class SetupApplication extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monica:setup
                            {--force : Force the operation to run when in production.}
                            {--skip-storage-link : Skip storage link create.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install or update the application, and run migrations after a new release';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if ($this->confirmToProceed()) {
            // Clear or rebuild all cache
            if (config('cache.default') != 'database' || Schema::hasTable(config('cache.stores.database.table'))) {
                $this->artisan('✓ Resetting application cache', 'cache:clear');
            }

            if ($this->getLaravel()->environment() == 'production') {
                // @codeCoverageIgnoreStart
                $this->artisan('✓ Clear config cache', 'config:clear');
                $this->artisan('✓ Resetting route cache', 'route:cache');
                $this->artisan('✓ Resetting view cache', 'view:clear');
            // @codeCoverageIgnoreEnd
            } else {
                $this->artisan('✓ Clear config cache', 'config:clear');
                $this->artisan('✓ Clear route cache', 'route:clear');
                $this->artisan('✓ Clear view cache', 'view:clear');
            }

            if ($this->option('skip-storage-link') !== true
                && $this->getLaravel()->environment() != 'testing'
                && ! file_exists(public_path('storage'))) {
                $this->artisan('✓ Symlink the storage folder', 'storage:link'); // @codeCoverageIgnore
            }

            $this->artisan('✓ Performing migrations', 'migrate', ['--force' => true]);

            // Cache config
            if ($this->getLaravel()->environment() == 'production'
                && (config('cache.default') != 'database' || Schema::hasTable(config('cache.stores.database.table')))) {
                $this->artisan('✓ Cache configuraton', 'config:cache'); // @codeCoverageIgnore
            }

            if (config('scout.driver') === 'meilisearch' && ($host = config('scout.meilisearch.host')) !== '') {
                $this->line('-> Creating indexes on Meilisearch. Make sure Meilisearch is running.');

                $client = new Client($host, config('scout.meilisearch.key'));
                $index = $client->index('contacts');
                $index->updateFilterableAttributes(['id', 'vault_id']);
                $index = $client->index('notes');
                $index->updateFilterableAttributes(['id', 'vault_id', 'contact_id']);
                $index = $client->index('groups');
                $index->updateFilterableAttributes(['id', 'vault_id']);

                $this->line('✓ Indexes created');
            }
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
