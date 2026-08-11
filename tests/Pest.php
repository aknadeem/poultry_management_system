<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that will be the PHPUnit\Framework\TestCase class. Of course, you
| may extend this class to override its behavior or add your own custom static methods.
|
*/

uses(
    Tests\TestCase::class,
    Tests\Support\FeatureRefreshDatabase::class,
)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of expectations that you can assert against
| your code. Of course, you may extend the Expectations API at any time.
|
*/

expect()->extend('toBeWithinRange', function ($min, $max) {
    return $this->toBeGreaterThanOrEqual($min)->toBeLessThanOrEqual($max);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| custom expects or "snake_case" functions for convenience.
|
*/

function something()
{
    // ..
}
