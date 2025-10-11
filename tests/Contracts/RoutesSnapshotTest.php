<?php

namespace Tests\Contracts;

use Tests\TestCase;

class RoutesSnapshotTest extends TestCase
{
    public function test_route_snapshot_is_up_to_date(): void
    {
        $this->artisan('contracts:routes:check')->assertExitCode(0);
    }
}
