<?php
namespace Modules\Account\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AccountServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('web')
            ->group(__DIR__ . '/../routes.php');
    }
}
