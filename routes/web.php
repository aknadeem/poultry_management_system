<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserManagement\{ UserController, UserLevelController};
Auth::routes();

Route::group(['middleware' => 'auth'], function(){
	Route::get('/', function () {
        return view('welcome');
    });
    Route::group(['prefix' => '/usermanagement'], function(){
        Route::get('/get-users-list', [UserController::class, 'getUsersList'])->name('getUsersList');
        Route::get('/get-userlevels', [UserController::class, 'getUserLevelList'])->name('getUserLevelList');
        Route::resource('users', UserController::class);
        Route::resource('userlevel', UserLevelController::class);
    });

    Route::get('/home', [HomeController::class, 'index'])->name('home');
});
