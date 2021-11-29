<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserManagement\UserController;
use App\Http\Controllers\PoultryShed\ {PoultryShedController, EmployeeController};
use App\Http\Controllers\PartyManagement\ {
    CustomerController,CompaniesController };

use App\Http\Controllers\InventoryManagement\ {FeedController, ExpenseController};
use App\Http\Controllers\ChickenModule\ {ChickenPurchaseController, ChickenSaleController};

Auth::routes();

Route::group(['middleware' => 'auth'], function(){
	Route::get('/', function () {
        return view('welcome');
    });

    Route::group(['prefix' => '/usermanagement'], function(){
        Route::get('/get-users-list', [UserController::class, 'getUsersList'])->name('getUsersList');
        Route::resource('users', UserController::class);
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

    Route::get('/getCompaniesList', [CompaniesController::class, 'getCompaniesList'])->name('getCompaniesList');

    Route::resource('company', CompaniesController::class)->except([
        'create', 'update'
    ]);
    Route::group(['prefix' => '/inventory'], function(){
        Route::get('/get-feed-list', [FeedController::class, 'getFeedList'])->name('getfeedlist');

        Route::resource('feed', FeedController::class)->except([
            'create', 'update'
        ]); 
        
        Route::get('/expense-categories', [ExpenseController::class, 'getExpenseCategoryList'])->name('getExpenseCategoryList');

        Route::get('/expense-list', [ExpenseController::class, 'getExpenseList'])->name('getExpenseList');
        
        Route::resource('expense', ExpenseController::class)->except([
            'create', 'update'
        ]);
    });

    Route::group(['prefix' => '/poultry'], function(){
        Route::get('/get-purchase-list', [ChickenPurchaseController::class, 'getPurchaseList'])->name('getpurchaselist');
        Route::resource('purchase', ChickenPurchaseController::class);
    });

    Route::group(['prefix' => '/poultry'], function(){
        Route::get('/get-sales-list', [ChickenSaleController::class, 'getSalesList'])->name('getSalesList');
        Route::resource('sale', ChickenSaleController::class);
    });

});
