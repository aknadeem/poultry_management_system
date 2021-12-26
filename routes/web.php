<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserManagement\{ UserController, UserLevelController};
use App\Http\Controllers\PoultryShed\ {
    PoultryShedController, EmployeeController, CustomerFarmController
};
use App\Http\Controllers\PartyManagement\ {
    PartyController, CustomerController, CompaniesController, CompaniesBalanceController, ConductPersonController, VendorController, PartyBalanceLimitController,
    PartyDocumentController, PartyAccountController, BrokerController
};

use App\Http\Controllers\InventoryManagement\ {FeedController, ExpenseController};
use App\Http\Controllers\ChickenModule\ {ChickenPurchaseController, ChickenSaleController, ChickPurchaseController};

Auth::routes();

Route::group(['middleware' => 'auth'], function(){
	Route::get('/', function () {
        return view('welcome');
    });

    Route::post('/store-alltypes', [VendorController::class, 'storeAllType'])->name('addalltypes');
    Route::put('/update-active-status/{id}/{tag}', [CompaniesController::class, 'updateStatus'])->name('updatestatus');

    Route::group(['prefix' => '/usermanagement'], function(){
        Route::get('/get-users-list', [UserController::class, 'getUsersList'])->name('getUsersList');
        Route::get('/get-userlevels', [UserController::class, 'getUserLevelList'])->name('getUserLevelList');
        Route::resource('users', UserController::class);
        Route::resource('userlevel', UserLevelController::class);
    });
    
    Route::group(['prefix' => '/partymanagement'], function(){
        Route::resource('parties', PartyController::class);
        Route::resource('vendors', VendorController::class);
        Route::resource('customers', CustomerController::class)->except(['update']);
        Route::resource('conductpersons', ConductPersonController::class);

        Route::resource('partydocuments', PartyDocumentController::class);
        Route::resource('partyaccounts', PartyAccountController::class);
        Route::resource('balancelimits', PartyBalanceLimitController::class);
        Route::resource('brokers', BrokerController::class);
    });
    
    Route::group(['prefix' => '/farmManagement'], function(){
        Route::resource('personalfarms', PoultryShedController::class);
        Route::resource('customerfarms', CustomerFarmController::class);
        Route::resource('employee', EmployeeController::class);
    });

    

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    Route::get('/getEmployeeList', [EmployeeController::class, 'getEmployeeList'])->name('getEmployeeList');
   

    Route::get('/getCompaniesList', [CompaniesController::class, 'getCompaniesList'])->name('getCompaniesList');
    
    Route::resource('company', CompaniesController::class)->except([
        'create', 'update'
    ]);

    Route::get('/getCompaniesBalanceList', [CompaniesBalanceController::class, 'getCompaniesBalanceList'])->name('getCompaniesBalanceList');
    
    Route::resource('companybalance', CompaniesBalanceController::class)->except([
        'create', 'update'
    ]);
    Route::group(['prefix' => '/inventory'], function(){
        Route::get('/get-feed-list', [FeedController::class, 'getFeedList'])->name('getfeedlist');

        Route::resource('feed', FeedController::class); 
        
        Route::get('/expense-categories', [ExpenseController::class, 'getExpenseCategoryList'])->name('getExpenseCategoryList');

        Route::get('/expense-list', [ExpenseController::class, 'getExpenseList'])->name('getExpenseList');
        
        Route::resource('expense', ExpenseController::class)->except([
            'create', 'update'
        ]);
    });

    Route::group(['prefix' => '/poultry'], function(){
        Route::get('/get-purchase-list', [ChickPurchaseController::class, 'getPurchaseList'])->name('getpurchaselist');
        Route::resource('purchase', ChickPurchaseController::class);
        Route::resource('chickenpurchase', ChickenPurchaseController::class);
    });

    Route::group(['prefix' => '/poultry'], function(){
        Route::get('/get-sales-list', [ChickenSaleController::class, 'getSalesList'])->name('getSalesList');
        Route::resource('sale', ChickenSaleController::class);
    });

});
