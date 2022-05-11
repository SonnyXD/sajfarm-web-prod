<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use \App\Models\Inventory;
use \App\Models\Category;
use \App\Models\Item;

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
        view()->composer('components.sidebar', function($view){
            $view->with('categories', Category::first()->slug);
        });

        view()->composer('components.sidebar', function($view){
            $view->with('inventories', Inventory::all());
        });

        view()->composer('components.subgestions', function($view){
            $view->with('categories', Category::all());
        });

        // view()->composer('components.medstable', function($view){
        //     $view->with('items', Item::all());
        // });

        view()->composer('components.medstable', function($view){
            $view->with('categories', Category::all());
        });
    }
}
