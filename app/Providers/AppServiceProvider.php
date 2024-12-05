<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\User;
use App\Policies\Core\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\Core\UserPolicy;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Stringable;

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
        FilamentShield::configurePermissionIdentifierUsing(
            fn($resource) => str($resource::getModel())
                ->afterLast('\\')
        );

        // Register Policies
        Gate::policy(User::class, UserPolicy::class);

        // Extend Stringable with a custom method
        Stringable::macro('whenNotContains', function ($needle, $callback, $default = null) {
            return $this->contains($needle) ? (is_callable($default) ? $default($this) : $this) : $callback($this);
        });
    }
}
