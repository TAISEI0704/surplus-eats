<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseHistory;

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
        View::composer('profile.show', function ($view) {
            $userId = Auth::user()->id;
    
            $purchaseHistories = PurchaseHistory::where('user_id', $userId)
                ->with('product', 'product.seller')
                ->latest('created_at')
                ->get();
    
            $view->with('latestPurchaseHistories', $purchaseHistories);
        });
    }
}
