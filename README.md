# TODO:
- [ ] Upgrade to Laravel 12

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
php artisan shield:generate --all
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
