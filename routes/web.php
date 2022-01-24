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

use App\Http\Controllers\BalanceManagement\BrokerBalanceController;

use App\Http\Controllers\InventoryManagement\ {FeedController, ExpenseController};
use App\Http\Controllers\ChickenModule\ {ChickenPurchaseController, ChickenSaleController, ChickPurchaseController};

use App\Http\Controllers\ProductManagement\ {
    ProductController, ProductPurchaseController, ProductStoreController, ProductSaleController
};

use App\Http\Controllers\PaymentManagement\AccountPayableController;

Auth::routes();

Route::get('/testpdf', function () {
    return view('productmanagement.sales.sale_detail');
    // return response()->file(storage_path('file.pdf'));
});

// Route::get('/testpdf', function () {
//     return response()->download(storage_path('file.pdf'), 'save-as-pdf');
// });

Route::group(['middleware' => 'auth'], function(){
	Route::get('/', function () {
        return view('welcome');
    });

    Route::post('/store-alltypes', [VendorController::class, 'storeAllType'])->name('addalltypes');
    Route::put('/update-active-status/{id}/{tag}', [CompaniesController::class, 'updateStatus'])->name('updatestatus');

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


    Route::group(['prefix' => '/usermanagement'], function(){
        Route::get('/get-users-list', [UserController::class, 'getUsersList'])->name('getUsersList');
        Route::get('/get-userlevels', [UserController::class, 'getUserLevelList'])->name('getUserLevelList');
        Route::resource('users', UserController::class);
        Route::resource('userlevel', UserLevelController::class);
    });
    
    Route::group(['prefix' => '/partymanagement'], function(){
        Route::get('/division-customers/{division_id}', [PartyController::class, 'customersWithDivision'])->name('division.customers');
        Route::resource('parties', PartyController::class);
        Route::resource('vendors', VendorController::class);
        Route::resource('customers', CustomerController::class)->except(['update']);
        Route::resource('conductpersons', ConductPersonController::class);

        Route::resource('partydocuments', PartyDocumentController::class);
        Route::resource('partyaccounts', PartyAccountController::class);
        Route::resource('balancelimits', PartyBalanceLimitController::class);
        Route::resource('brokers', BrokerController::class);
    });

    Route::group(['prefix' => '/balancemanagement'], function(){
        Route::get('/getbrokersBalanceList', [BrokerBalanceController::class, 'getbrokersBalanceList'])->name('getbrokersBalanceList');
        Route::resource('brokerbalance', BrokerBalanceController::class);

        Route::get('/balance-with-company/{id}', [CompaniesBalanceController::class, 'getBalanceWithCompany'])->name('getBalanceWithCompany');

        Route::resource('companybalance', CompaniesBalanceController::class)->except([
            'create', 'update'
        ]);
    });
    
    Route::group(['prefix' => '/farmManagement'], function(){
        Route::resource('personalfarms', PoultryShedController::class);
        Route::resource('customerfarms', CustomerFarmController::class);
        Route::resource('employee', EmployeeController::class);
    });


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

        Route::get('/get-sales-list', [ChickenSaleController::class, 'getSalesList'])->name('getSalesList');
        Route::resource('sale', ChickenSaleController::class);
    });
    
    Route::group(['prefix' => '/ProductManagement'], function(){
        Route::get('/productfilter/{company_id}/cat/{category_id}', [ProductController::class, 'companyAndCategoryFilter'])->name('productfilter');

        Route::resource('products', ProductController::class);
        Route::resource('productpurchases', ProductPurchaseController::class);

        Route::get('/storelist', [ProductStoreController::class, 'getStoreList'])->name('storelist');
        Route::resource('productstores', ProductStoreController::class);
        Route::resource('productsales', ProductSaleController::class);
    });

    Route::group(['prefix' => '/paymentmanagement'], function(){
        Route::resource('payables', AccountPayableController::class);
    });
    
});
