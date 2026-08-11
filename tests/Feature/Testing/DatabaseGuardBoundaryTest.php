<?php

use Tests\Support\TestingDatabaseGuard;

it('runs the database guard during createApplication before refresh database traits', function () {
    expect(array_slice(TestingDatabaseGuard::$invocationLog, -2))->toBe(['guard', 'refresh']);
});
