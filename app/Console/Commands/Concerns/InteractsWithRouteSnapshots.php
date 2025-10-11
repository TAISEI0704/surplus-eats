<?php

namespace App\Console\Commands\Concerns;

use Illuminate\Support\Facades\Artisan;
use JsonException;

trait InteractsWithRouteSnapshots
{
    /**
     * Resolve the absolute path to the stored route snapshot.
     */
    protected function snapshotFilePath(): string
    {
        return base_path('tests/Contracts/__snapshots__/routes.json');
    }

    /**
     * Build a normalised representation of the current route table.
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws JsonException
     */
    protected function currentRouteSnapshot(): array
    {
        Artisan::call('route:list', ['--json' => true]);

        $json = Artisan::output();

        return $this->normaliseRoutes(
            $this->decodeJson($json)
        );
    }

    /**
     * Decode the JSON payload returned by `route:list --json`.
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws JsonException
     */
    protected function decodeJson(string $json): array
    {
        try {
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new JsonException('Unable to decode route list JSON.', 0, $exception);
        }

        if (! is_array($decoded)) {
            throw new JsonException('Route list JSON did not decode to an array.');
        }

        return $decoded;
    }

    /**
     * Normalise and sort the route table for stable snapshots.
     *
     * @param  array<int, array<string, mixed>>  $routes
     * @return array<int, array<string, mixed>>
     */
    protected function normaliseRoutes(array $routes): array
    {
        return collect($routes)
            ->map(function (array $route): array {
                $middleware = array_values(isset($route['middleware']) ? (array) $route['middleware'] : []);
                sort($middleware);

                return [
                    'domain' => $route['domain'] ?? null,
                    'method' => $route['method'] ?? '',
                    'uri' => $route['uri'] ?? '',
                    'name' => $route['name'] ?? null,
                    'action' => $route['action'] ?? '',
                    'middleware' => $middleware,
                ];
            })
            ->sortBy([
                ['domain', 'asc'],
                ['uri', 'asc'],
                ['method', 'asc'],
                ['name', 'asc'],
            ])
            ->values()
            ->all();
    }

    /**
     * Encode the snapshot payload with a predictable format.
     *
     * @param  array<int, array<string, mixed>>  $snapshot
     */
    protected function encodeSnapshot(array $snapshot): string
    {
        $encoded = json_encode(
            $snapshot,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        if ($encoded === false) {
            throw new JsonException('Unable to encode route snapshot to JSON.');
        }

        return $encoded."\n";
    }
}
