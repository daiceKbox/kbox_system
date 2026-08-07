<?php

namespace App\Providers;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive("num", function($expression) {
            return "<?php echo (float) str_replace(',', '', {$expression} ?? 0); ?>";
        });
        Blade::directive("price", function($expression) {
            return "<?php
                        \$val = (float) str_replace(',', '', {$expression} ?? 0);
                        echo number_format(\$val, 2, '.', '');
                    ?>";
        });
        Blade::directive("date", function($expression) {
            return  "<?php
                        try {
                            \$val = {$expression};
                            echo    (empty(\$val) || \$val === '0000-00-00 00:00:00' || \$val === '0000-00-00')
                                ?   ''
                                :   \Carbon\Carbon::parse(\$val)->format('Y-m-d');
                        } catch (\\Exception \$e) {
                            echo '';
                        }
                    ?>";
        });
    }
}
