<?php

namespace App\Providers;

// use App\Contracts\TestInterfaceLog;
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

        Blade::directive('money', function ($amount) {
            return "<?php echo number_format($amount, 2); ?>";
        });
        
        Blade::directive('uppercaseFirst', function ($my_str) {
            return "<?php echo ucfirst(str_replace('_', ' ', $my_str)); ?>";
        });
        // $this->app->bind(TestInterfaceLog::class);
    }
}
