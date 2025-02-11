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

# Modules
Check this documentation https://filamentphp.com/plugins/coolsam-modules

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
git push origin master
```

**Push all branches to `origin` (new repo)**
```bash
git push --all
```