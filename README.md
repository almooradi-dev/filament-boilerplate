# TODO:
- [x] Upgrade to Laravel 12
- [ ] Add documentation for:
    - [ ] Notifications
    - [ ] Google Analytics
- [ ] Add link to https://github.com/alexeymezenin/laravel-best-practices#follow-laravel-naming-conventions as a reference
- [ ] Themes
    - [ ] Theme 01

# Setup
**Migrate**
```bash
php artisan migrate:fresh --seed
```
Admin credentials:
- Email: super_admin@example.com
- Password: 12345678


**Generate permissions and assign to super admin**
```bash
php artisan shield:generate --all --ignore-existing-policies --panel=admin
```

**Link storage**
```bash
php artisan storage:link
```

**Encryption Key**
```bash
php artisan key:generate
```

**Disable Debug Bar** (optional)
```bash
DEBUGBAR_ENABLED=false # Add to .env
```

**Scheduler** (if needed)
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

# Optimizing Filament for production

```bash
php artisan filament:optimize
```

More details here: https://filamentphp.com/docs/3.x/panels/installation#improving-filament-panel-performance

# Roles & Permissions

- https://spatie.be/docs/laravel-permission/v6/installation-laravel
- https://filamentphp.com/plugins/bezhansalleh-shield

### Name Convention Used
{action}_{ResourceModelName}

Examples:
- viewAny_User
- create_UserType
- forceDeleteAny_PostCategory

# Modules
- [https://nwidart.com/laravel-modules/v6/introduction](https://nwidart.com/laravel-modules/v6/introduction)
- [https://filamentphp.com/plugins/coolsam-modules](https://filamentphp.com/plugins/coolsam-modules)


```bash
php artisan module:make-model ChallengeJudgingFormQuestion ChallengeJudging -m
```

## Common issues
- ### Class "Modules\{Module}\Providers\{Module}ServiceProvider" not found
    Use the code below in `composer.json` (https://stackoverflow.com/a/79306021)
    ```json
    "extra": {
        "laravel": {
            "dont-discover": []
        },
        "merge-plugin": {
            "include": [
                "Modules/*/composer.json"
            ]
        }
    },
    ```
    Then run `composer dump-autoload`

# Helpful Links
- [https://github.com/filamentphp/filament/discussions/9012#discussioncomment-7246013](https://github.com/filamentphp/filament/discussions/9012#discussioncomment-7246013)

# Upgrade from v3 to v4
```bash
composer require filament/upgrade:"^4.0" -W --dev
```

```bash
vendor/bin/filament-v4
```

## Packages
- Run
    ```bash
    composer require filament/filament:"^4.0" -W --no-update
    composer require filament/spatie-laravel-settings-plugin:"^4.0" -W --no-update
    composer remove filament/spatie-laravel-translatable-plugin -W --no-update
    composer require lara-zeus/spatie-translatable -W --no-update
    composer require bezhansalleh/filament-shield:"^4.0" -W --no-update
    composer require coolsam/modules:"^5.0" -W --no-update
    ```

- Remove `awcodes/filament-table-repeater` from `composer.json`, temporarily

- Replace `use Filament\Forms\Get;` with `use Filament\Schemas\Components\Utilities\Get;`
- Replace `use Filament\Forms\Set;` with `use Filament\Schemas\Components\Utilities\Set;`
- Replace `\Filament\SpatieLaravelTranslatablePlugin::make()` with `\LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin::make()`
- Replace `protected static string $view` with `protected string $view`

- And then run
    ```bash
    composer update
    ```
- Re-run the command below to re-generate permisions in the new pattern
    ```bash
    php artisan shield:generate --all --panel=admin
    ```

## Errors
- **Object of class BezhanSalleh\FilamentShield\Support\ShieldConfig could not be converted to string**
    Delete `config/filament-shield.php` and re-publish it later using:
    ```bash
    php artisan vendor:publish --tag="filament-shield-config"
    ```
- **Class "Filament\Support\Enums\MaxWidth" not found at app/Providers/Filament/AdminPanelProvider.php:39**
    - Remove `use Filament\Support\Enums\MaxWidth;`
    - Comment `->maxContentWidth(MaxWidth::Full)`

- **Trait "Filament\Resources\Concerns\Translatable" not found**
    - Replace `use Filament\Resources\Concerns\Translatable;` with `use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;`
    - Replace the Trait `use CreateRecord\Concerns\Translatable` with `use Translatable` only, and import using `use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;`
    - Replace the Trait `use EditRecord\Concerns\Translatable` with `use Translatable` only, and import using `use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;`
    - Replace the Trait `use ListRecords\Concerns\Translatable` with `use Translatable` only, and import using `use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;`

- **Type of App\Filament\Resources\Core\PageResource::$navigationIcon must be BackedEnum|string|null (as in class Filament\Resources\Resource)**
    - Replace `protected static ?string $navigationIcon` with `protected static BackedEnum|string|null $navigationIcon`
    - Don't forget to import `BackedEnum` using `use BackedEnum;`

- **Could not check compatibility between Class\Path::form(Filament\Forms\Form $form): Filament\Forms\Form and Filament\Resources\Resource::form(Filament\Schemas\Schema $schema): Filament\Schemas\Schema, because class Filament\Forms\Form is not available**
    - Replace `function form(Form $form): Form` with `function schema(Schema $schema): Schema`
    - Don't forget to import `Schema` using `use Filament\Schemas\Schema;`
    - Replace 
        ```php
        return $form
            ->schema
        ```
        With
        ```php
        return $schema
            ->components
        ```
    - Replace `use Filament\Forms\Components\Grid` with `use Filament\Schemas\Components\Grid`
- 

# Fork??
**Clone the boilerplate repo**
```bash
git clone https://github.com/almooradi-dev/filament-boilerplate.git new_repo
cd new_repo
```

**Change the `origin` remote to point to a new repository**
```bash
git remote set-url origin https://github.com/username/new_repo
```

**Add the original repo as an `upstream` remote**
```bash
git remote add upstream https://github.com/almooradi-dev/filament-boilerplate.git
```

**Push the cloned code to the new repository**
```bash
git push origin main
```

**Push all branches to `origin` (new repo)**
```bash
git push --all
```

**Get changes from `upstream`**
```bash
git fetch upstream
git merge upstream/main
```
