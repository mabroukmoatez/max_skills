<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Http\Livewire\CreateCourse;
use App\Http\Livewire\ManageChapters;
use Illuminate\Support\Facades\View;
use App\Models\User;

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
        View::composer('*', function ($view) {
            $nb_client = User::where('role', 'client')->where('status','!=',2)->count(); // Count the number of clients
            $view->with('nb_client', $nb_client);
        });
    }
}
