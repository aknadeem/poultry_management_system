<?php

use Illuminate\Support\ConfigurationUrlParser;
use Tests\Support\TestingDatabaseGuard;

beforeEach(function () {
    TestingDatabaseGuard::$invocationLog = [];
});

it('accepts sqlite in-memory configuration', function () {
    TestingDatabaseGuard::assertInMemorySqlite([
        'default' => 'sqlite',
        'connections' => [
            'sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
            ],
        ],
    ]);

    expect(TestingDatabaseGuard::$invocationLog)->toBe(['guard']);
});

it('accepts sqlite in-memory DATABASE_URL override', function () {
    TestingDatabaseGuard::assertInMemorySqlite([
        'default' => 'sqlite',
        'connections' => [
            'sqlite' => [
                'driver' => 'sqlite',
                'url' => 'sqlite:///:memory:',
                'database' => '/tmp/database.sqlite',
            ],
        ],
    ]);

    expect(TestingDatabaseGuard::$invocationLog)->toBe(['guard']);
});

it('rejects mysql configuration', function () {
    TestingDatabaseGuard::assertInMemorySqlite([
        'default' => 'mysql',
        'connections' => [
            'mysql' => [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'database' => 'forge',
            ],
        ],
    ]);
})->throws(RuntimeException::class, 'Feature tests require sqlite/:memory:');

it('rejects file-based sqlite configuration', function () {
    TestingDatabaseGuard::assertInMemorySqlite([
        'default' => 'sqlite',
        'connections' => [
            'sqlite' => [
                'driver' => 'sqlite',
                'database' => '/tmp/database.sqlite',
            ],
        ],
    ]);
})->throws(RuntimeException::class, 'Feature tests require sqlite/:memory:');

it('rejects mysql DATABASE_URL override on sqlite connection', function () {
    TestingDatabaseGuard::assertInMemorySqlite([
        'default' => 'sqlite',
        'connections' => [
            'sqlite' => [
                'driver' => 'sqlite',
                'url' => 'mysql://root:secret@127.0.0.1:3306/forge',
                'database' => ':memory:',
            ],
        ],
    ]);
})->throws(RuntimeException::class, 'Feature tests require sqlite/:memory:');

it('rejects file sqlite DATABASE_URL override', function () {
    TestingDatabaseGuard::assertInMemorySqlite([
        'default' => 'sqlite',
        'connections' => [
            'sqlite' => [
                'driver' => 'sqlite',
                'url' => 'sqlite:////tmp/testing.sqlite',
                'database' => ':memory:',
            ],
        ],
    ]);
})->throws(RuntimeException::class, 'Feature tests require sqlite/:memory:');

it('resolves effective config using the same parser as Laravel database manager', function () {
    $databaseConfig = [
        'default' => 'sqlite',
        'connections' => [
            'sqlite' => [
                'driver' => 'sqlite',
                'url' => 'sqlite:///:memory:',
                'database' => '/tmp/database.sqlite',
            ],
        ],
    ];

    $expected = (new ConfigurationUrlParser)->parseConfiguration($databaseConfig['connections']['sqlite']);

    expect(TestingDatabaseGuard::resolveEffectiveConnectionConfig($databaseConfig))
        ->toBe($expected);
});
