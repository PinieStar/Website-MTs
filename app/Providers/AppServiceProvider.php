<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
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
        Gate::define('manage-admins', fn (User $user): bool => $user->isSuperAdmin());

        Gate::define('access-module', fn (User $user, string $module): bool => $user->canAccessModule($module));

        // Tombol pagination bertema situs, dipakai halaman publik dan panel admin.
        Paginator::defaultView('vendor.pagination.site');
        Paginator::defaultSimpleView('vendor.pagination.site');
    }
}
