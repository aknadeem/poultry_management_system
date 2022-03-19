<?php

namespace App\Providers;

// use App\Contracts\TestInterfaceLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::unguard();
        \URL::forceScheme('https');

        // if($this->app->environment('production')) {
        //     \URL::forceScheme('https');
        // }

        Blade::directive('money', function ($amount) {
            return "<?php echo number_format($amount, 0); ?>";
        });
        Blade::directive('uppercaseFirst', function ($my_str) {
            return "<?php echo ucfirst(str_replace('_', ' ', $my_str)); ?>";
        });
        Blade::directive('Status', function ($my_status) {
            $status = '';
            if($my_status == 1){
                $status = '<span class="badge bg-danger"> UnPaid </span>';
            }else if($my_status == 2){
                $status = '<span class="badge bg-warning"> Pending </span>';
            }else if($my_status == 3){
                $status = '<span class="badge bg-success"> Paid </span>';
            }
            return $status;
        });
        Blade::directive('Statuss', function ($my_status) {
            $status = '';
            if($my_status == '1'){
                $status = '<span class="badge bg-danger"> UnPaid </span>';
            }else if($my_status == '2'){
                $status = '<span class="badge bg-warning"> Pending </span>';
            }else if($my_status == '3'){
                $status = '<span class="badge bg-success"> Paid </span>';
            }
            return $status;
        });
        // $this->app->bind(TestInterfaceLog::class);

        DB::listen(function ($query) {
            $location = collect(debug_backtrace())->filter(function ($trace) {
                return !str_contains($trace['file'], 'vendor/');
            })->first(); // grab the first element of non vendor/ calls

            $bindings = implode(", ", $query->bindings); // format the bindings as string

            Log::info("
                ------------
                Sql: $query->sql
                Bindings: $bindings
                Time: $query->time
                File: ${location['file']}
                Line: ${location['line']}
                ------------
            ");
        });
    }
}
