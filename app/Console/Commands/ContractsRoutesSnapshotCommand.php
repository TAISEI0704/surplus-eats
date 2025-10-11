<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\InteractsWithRouteSnapshots;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use JsonException;

class ContractsRoutesSnapshotCommand extends Command
{
    use InteractsWithRouteSnapshots;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:routes:snapshot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store the current route list snapshot for contract verification.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $snapshot = $this->currentRouteSnapshot();
        } catch (JsonException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $path = $this->snapshotFilePath();
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $this->encodeSnapshot($snapshot));

        $this->components->info("Route snapshot stored at: {$path}");

        return self::SUCCESS;
    }
}
