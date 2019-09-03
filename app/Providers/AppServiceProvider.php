<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('validator', function($expression) {
            return "<?php echo App\\Common\\ClientValidator::renderValidation($expression); ?>";
        });
        Blade::directive('validatorMessage', function($expression) {
            return "<?php echo App\\Common\\ClientValidator::renderValidationMessage($expression); ?>";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
