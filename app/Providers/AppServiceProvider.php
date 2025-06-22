<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\Core\UserPolicy;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Stringable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTranslations;
use Exception;

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

        /**
         * Translate collection data (Mainly used for API or JSON responses)
         */
        Collection::macro('translate', function ($locale = null) {
            $locale = $locale ?? app()->getLocale();

            return $this->map(function ($item) use ($locale): array {
                $itemArray = null;

                if (in_array(HasTranslations::class, class_uses($item))) {
                    $itemArray = $item->translate($locale);
                } else {
                    $itemArray = $item->toArray();
                }

                foreach ($item->getRelations() as $relationKey => $relation) {
                    if ($relation instanceof Model || $relation instanceof Collection) {
                        try {
                            $itemArray[$relationKey] = $relation->translate($locale);
                        } catch (Exception $e) {
                            // ! We got an error when using "pivot" relation like "BelongsToMany"
                            $itemArray[$relationKey] = $relation;
                        }
                    }
                }

                return $itemArray ?? $item->toArray();
            });
        });
    }
}
