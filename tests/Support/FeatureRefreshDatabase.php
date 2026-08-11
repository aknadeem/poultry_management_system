<?php

namespace Tests\Support;

use Illuminate\Foundation\Testing\RefreshDatabase;

trait FeatureRefreshDatabase
{
    use RefreshDatabase {
        refreshDatabase as laravelRefreshDatabase;
    }

    public function refreshDatabase()
    {
        TestingDatabaseGuard::$invocationLog[] = 'refresh';

        $this->laravelRefreshDatabase();
    }
}
