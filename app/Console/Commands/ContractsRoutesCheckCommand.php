<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\InteractsWithRouteSnapshots;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use JsonException;

class ContractsRoutesCheckCommand extends Command
{
    use InteractsWithRouteSnapshots;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:routes:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate that the saved route snapshot matches the current application routes.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = $this->snapshotFilePath();

        if (! File::exists($path)) {
            $this->components->error('Route snapshot file not found. Run `php artisan contracts:routes:snapshot` to generate it.');

            return self::FAILURE;
        }

        try {
            $expected = $this->normaliseRoutes(
                $this->decodeJson(File::get($path))
            );
            $current = $this->currentRouteSnapshot();
        } catch (JsonException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        if ($expected !== $current) {
            $this->components->error('Route snapshot is outdated. Run `php artisan contracts:routes:snapshot` and commit the updated file.');

            return self::FAILURE;
        }

        $this->components->info('Route snapshot is up to date.');

        return self::SUCCESS;
    }
}
