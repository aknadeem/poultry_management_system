<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Tests\Support\TestingDatabaseGuard;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        TestingDatabaseGuard::assertApplicationUsesInMemorySqlite($app);

        return $app;
    }
}
