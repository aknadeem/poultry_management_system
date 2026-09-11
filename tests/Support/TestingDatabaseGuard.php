<?php

namespace Tests\Support;

use Illuminate\Support\ConfigurationUrlParser;
use RuntimeException;

class TestingDatabaseGuard
{
    /** @var list<string> */
    public static array $invocationLog = [];

    /**
     * @param  array<string, mixed>  $databaseConfig
     * @return array<string, mixed>
     */
    public static function resolveEffectiveConnectionConfig(array $databaseConfig, ?string $connectionName = null): array
    {
        $connectionName = $connectionName ?? ($databaseConfig['default'] ?? null);

        if (! is_string($connectionName) || $connectionName === '') {
            throw new RuntimeException('Feature tests require a configured default database connection.');
        }

        $connections = $databaseConfig['connections'] ?? [];
        $rawConfig = $connections[$connectionName] ?? null;

        if (! is_array($rawConfig)) {
            throw new RuntimeException("Database connection [{$connectionName}] is not configured.");
        }

        return (new ConfigurationUrlParser)->parseConfiguration($rawConfig);
    }

    /**
     * @param  array<string, mixed>  $databaseConfig
     */
    public static function assertInMemorySqlite(array $databaseConfig, ?string $connectionName = null): void
    {
        self::assertSafeTestingDatabase($databaseConfig, $connectionName);
    }

    /**
     * Allow sqlite/:memory: by default, or MySQL/MariaDB databases whose name ends in `_testing`.
     *
     * @param  array<string, mixed>  $databaseConfig
     */
    public static function assertSafeTestingDatabase(array $databaseConfig, ?string $connectionName = null): void
    {
        self::$invocationLog[] = 'guard';

        $connectionName = $connectionName ?? ($databaseConfig['default'] ?? 'unknown');
        $effective = self::resolveEffectiveConnectionConfig($databaseConfig, is_string($connectionName) ? $connectionName : null);

        $driver = $effective['driver'] ?? null;
        $database = $effective['database'] ?? null;

        if ($driver === 'sqlite' && $database === ':memory:') {
            return;
        }

        if (in_array($driver, ['mysql', 'mariadb'], true)
            && is_string($database)
            && str_ends_with($database, '_testing')
        ) {
            return;
        }

        throw new RuntimeException(
            "Feature tests require sqlite/:memory: or a MySQL/MariaDB database ending in _testing, got [{$connectionName}/{$driver}/{$database}]."
        );
    }

    public static function assertApplicationUsesInMemorySqlite(\Illuminate\Contracts\Foundation\Application $app): void
    {
        self::assertSafeTestingDatabase($app['config']['database']);
    }
}
