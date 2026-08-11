<?php

use App\Http\Controllers\PartyManagement\PartyController;
use ReflectionMethod;

it('exposes public update and destroy methods for linked party forms', function () {
    $update = new ReflectionMethod(PartyController::class, 'update');
    $destroy = new ReflectionMethod(PartyController::class, 'destroy');

    expect($update->isPublic())->toBeTrue();
    expect($destroy->isPublic())->toBeTrue();
});
