<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    public function boot(): void
    {
        $landlordMenuJson = file_get_contents(base_path('resources/menu/landlordMenu.json'));
        $userMenuJson = file_get_contents(base_path('resources/menu/userMenu.json'));
        $adminMenuJson = file_get_contents(base_path('resources/menu/adminMenu.json'));

        $landlordMenu = json_decode($landlordMenuJson);
        $userMenu = json_decode($userMenuJson);
        $adminMenu = json_decode($adminMenuJson);

        View::composer('*', function ($view) use ($landlordMenu, $userMenu, $adminMenu) {
            $user = Auth::user();

            if ($user && $user->role === 'landlord') {
                $view->with('menuData', [$landlordMenu]);
            } elseif ($user && $user->role === 'admin') {
                $view->with('menuData', [$adminMenu]);
            } else {
                $view->with('menuData', [$userMenu]);
            }
        });
    }
}