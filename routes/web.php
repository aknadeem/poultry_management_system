<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PoultryShed\ {PoultryShedController, EmployeeController};
use App\Http\Controllers\PartyManagement\ {
    CustomerController,CompaniesController };

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::group(['middleware' => 'auth'], function(){
	Route::get('/', function () {
        return view('welcome');
    });    
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::resource('poultryshed', PoultryShedController::class);
    Route::resource('employee', EmployeeController::class)->except([
        'create', 'update'
    ]);

    Route::get('/getEmployeeList', [EmployeeController::class, 'getEmployeeList'])->name('getEmployeeList');

    Route::resource('customer', CustomerController::class)->except([
        'create', 'update'
    ]);
    Route::resource('company', CompaniesController::class)->except([
        'create', 'update'
    ]);
});
