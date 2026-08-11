<?php

use App\Http\Controllers\PartyManagement\PartyController;
use Illuminate\Support\Facades\Route;
use ReflectionMethod;

it('has unique named routes', function () {
    $names = collect(Route::getRoutes())
        ->map(fn ($route) => $route->getName())
        ->filter();

    expect($names->duplicates()->values()->all())->toBe([]);
});

it('only registers existing public controller methods', function () {
    $missing = collect(Route::getRoutes())
        ->map(fn ($route) => $route->getActionName())
        ->filter(fn ($action) => str_contains($action, '@'))
        ->reject(function ($action) {
            [$controller, $method] = explode('@', $action);

            if (! class_exists($controller) || ! method_exists($controller, $method)) {
                return false;
            }

            $reflection = new ReflectionMethod($controller, $method);

            return $reflection->isPublic();
        })
        ->values()
        ->all();

    expect($missing)->toBe([]);
});

it('generates the chick purchase report page without dates', function () {
    expect(route('chickreport.purchases', absolute: false))
        ->toBe('/reportmanagement/chick-purchases');
});

it('registers parties update and destroy routes referenced by party views', function () {
    expect(Route::has('parties.update'))->toBeTrue();
    expect(Route::has('parties.destroy'))->toBeTrue();

    $updateRoute = Route::getRoutes()->getByName('parties.update');
    $destroyRoute = Route::getRoutes()->getByName('parties.destroy');

    expect($updateRoute->getActionName())->toBe(PartyController::class.'@update');
    expect($destroyRoute->getActionName())->toBe(PartyController::class.'@destroy');
});
