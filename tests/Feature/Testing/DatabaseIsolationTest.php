<?php

use Tests\Support\TestingDatabaseGuard;

it('uses only an in-memory sqlite database', function () {
    $effective = TestingDatabaseGuard::resolveEffectiveConnectionConfig(config('database'));

    expect($effective['driver'])->toBe('sqlite')
        ->and($effective['database'])->toBe(':memory:');
});
