# Setup
**Migrate**
```
php artisan migrate:fresh --seed
```

**Generate permissions and assign to super admin**
```
php artisan shield:generate --all
```

**Link storage**
```
php artisan storage:link
```

# Optimizing Filament for production

```
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