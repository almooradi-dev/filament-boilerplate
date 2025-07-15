<?php

namespace App\Providers;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Stringable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTranslations;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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

        // Register Policies (TODO: Auto register policies lavael base app (core)) (TODO: Add to docs, that policies are auto registered)
        $this->registerModulePolicies();
        $this->registerAppPolicies();
        // Gate::policy(User::class, UserPolicy::class);

        // Extend Stringable with a custom method
        Stringable::macro('whenNotContains', function ($needle, $callback, $default = null) {
            return $this->contains($needle) ? (is_callable($default) ? $default($this) : $this) : $callback($this);
        });

        // Extend Builder with a custom method
        Builder::macro('whereFalsy', function ($column) {
            return $this->where(function ($query) use ($column) {
                $query->whereNull($column)
                    ->orWhere($column, '')
                    ->orWhere($column, 0)
                    ->orWhere($column, false);
            });
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

        // $this->registerMigrationSqlLogger(); // TODO: Add to docs
    }

    protected function registerMigrationSqlLogger(): void
    {
        // Log executed SQL queries **only** during migration-related Artisan commands.
        // This is useful for environments without SSH, where migrations are run locally
        // and SQL needs to be manually copied to a remote server (e.g., via phpMyAdmin).

        if ($this->app->runningInConsole() && $this->isRunningMigrationArtisanCommand()) {
            DB::listen(function ($query) {
                $sql = $query->sql;
                foreach ($query->bindings as $binding) {
                    $binding = is_numeric($binding) ? $binding : "'" . addslashes($binding) . "'";
                    $sql = preg_replace('/\?/', $binding, $sql, 1);
                }

                file_put_contents(
                    storage_path('logs/migrations.sql'),
                    $sql . ";\n",
                    FILE_APPEND
                );
            });
        }
    }

    protected function isRunningMigrationArtisanCommand(): bool
    {
        $commands = [
            'migrate',
            'migrate:rollback',
            'migrate:refresh',
            'migrate:fresh',
            'migrate:reset'
        ];

        return isset($_SERVER['argv'][1]) && in_array($_SERVER['argv'][1], $commands);
    }

    protected function registerModulePolicies()
    {
        $modulesPath = base_path('Modules');
        $modules = File::directories($modulesPath);

        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);

            $modelsPath = $modulePath . '/Models';
            $policiesPath = $modulePath . '/Policies';

            if (!File::isDirectory($modelsPath) || !File::isDirectory($policiesPath)) {
                continue;
            }

            $modelFiles = File::files($modelsPath);

            foreach ($modelFiles as $modelFile) {
                $modelClass = "Modules\\$moduleName\\Models\\" . $modelFile->getFilenameWithoutExtension();
                $policyClass = "Modules\\$moduleName\\Policies\\" . $modelFile->getFilenameWithoutExtension() . 'Policy';

                if (class_exists($modelClass) && class_exists($policyClass)) {
                    Gate::policy($modelClass, $policyClass);
                }
            }
        }
    }

    protected function registerAppPolicies()
    {
        $modelsPath = app_path('Models');
        $policiesPath = app_path('Policies');

        if (!File::isDirectory($modelsPath) || !File::isDirectory($policiesPath)) {
            return;
        }

        $modelFiles = File::allFiles($modelsPath);

        foreach ($modelFiles as $modelFile) {
            $relativePath = Str::replaceFirst($modelsPath . DIRECTORY_SEPARATOR, '', $modelFile->getPathname());
            $relativeClass = str_replace([DIRECTORY_SEPARATOR, '.php'], ['\\', ''], $relativePath);
            $modelClass = "App\\Models\\$relativeClass";
            $policyClass = "App\\Policies\\$relativeClass" . 'Policy';

            if (class_exists($modelClass) && class_exists($policyClass)) {
                Gate::policy($modelClass, $policyClass);
            }
        }
    }
}
