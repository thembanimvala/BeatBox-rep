<p align="center">
<a href="https://laravel.com" target="_blank"></a></p>

## Laravel Install

````bash
laravel new multi-tenant
````
- Edit the .env add the dbase and mailtrap

````bash
php artisan storage:link        # Create public storage for file uploads
php artisan key:generate        # Set the application key
php artisan migrate
php artisan serve
````

- The base APP with dbase up and running
- Check home page in browser

### [Filament](https://filamentphp.com/docs/3.x/panels/installation) install

- Choose/Enter 'admin' as the default panel below for 'Superuser' users 

````bash
composer require filament/filament:"^3.0-stable" -W
php artisan vendor:publish --tag=filament-config
php artisan filament:install --panels
php artisan make:filament-user
npm install
npm run dev
````

- In your ide look inside App\Providers\Filament\AdminPanelProvider

### Login to the APP

- /admin is the path
- Nothing much to see, boilerplate is running!

# Install [Spatie Laravel Permissions](https://spatie.be/docs/laravel-permission/v6/installation-laravel)

````bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan optimize:clear
php artisan migrate
````

- Add the HasRoles trait to the User model

````php
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
````

- Now goto database seeder re-add the user we created for Filament, add a Superuser role make them Superuser
- Also add a test user with no role so we can see test how this works

````php
    $user = User::factory()->create([
         'name' => 'Naude',
         'email' => 'naude@carsalesportal.co.za',
     ]);
    $role = Role::create(['name' => 'Superuser']);
    $user = User::find(1);
    $user->assignRole($role);
    User::factory()->create([
            'name' => 'Test',
            'email' => 'test@carsalesportal.co.za',
        ]);
````

- Migrate & seed the dbase again

````bash
php artisan migrate:fresh --seed
````

- Look in the database factory the new user has a 'password' password 
- login to the APP again with 'Superuser' user and see it's running :-)
- login with 'test' user and see that!
- Add Filament authorizing to the 'admin' panel [Docs](https://filamentphp.com/docs/3.x/panels/users)
- User.php model

````php
class User extends Authenticatable implements FilamentUser

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('Superuser');
    }
````

- Test by logging in for both users

### Create Roles Permissions User Management in the admin panel

- Login to the dbase and look describe the spatie tables
  - roles
  - permissions
  - model_has_permissions
  - model_has_roles        

````bash
php artisan make:filament-resource Role
php artisan make:filament-resource Permission
````

- Look inside \App\Filament
- PS: We can't use the --generate on the above since the model is Spatie and Filament is looking in App\Models (we will use this later)
- Edit the RoleResource and PermissionResource
- Replace the default App\Models\Role with the Spatie role ... ditto for Permission
- We will change this below [Harmonise Role & Model](#harmonise-role-permission-models)

````php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
````

- Then we add the form and table variables for name

````php
return $form
    ->schema([
        TextInput::make('name')
            ->required()
            ->unique()
            ->minLength(3)
            ->maxLength(255),
    ]);
    
return $table
    ->columns([
        TextColumn::make('name')
            ->searchable()
            ->sortable(),
    ])
````

- Check the APP list edit etc
- To use the filament make resource with --generate must install doctrine

````bash
composer require doctrine/dbal --dev
````

- Create a User resource

````bash
php artisan make:filament-resource User --generate --view
````

- Set the password required on create and don't update on edit if blank

````php
Forms\Components\TextInput::make('password')
    ->password()
    ->minLength(8)
    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
    ->dehydrated(fn (?string $state): bool => filled($state))
    ->required(fn (string $operation): bool => $operation === 'create'),
````

- at rolesresource Add multi-select dropdown for permissions to role and roles to user, under return forms

````php
Select::make('permission')
    ->multiple()
    ->searchable()
    ->getSearchResultsUsing(fn (string $search): array => Permission::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
    ->getOptionLabelsUsing(fn (array $values): array => Permission::whereIn('id', $values)->pluck('name', 'id')->toArray())
    ->relationship('permissions', 'name')
    ->preload(),
````

- Hide the 'Superuser' role from users so that they can't delete edit it inside the RoleResource place under the getPages() function

````php
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()->where('name', '!=', 'Superuser');
}
````

## Policy Required on models to implement Permissions

- **Create policies for all Models as the app grows**

````bash
php artisan make:policy UserPolicy --model=User
````

- Add all the permissions as functions 
- Your APP must always check for permissions (as much as possible), not roles

````php
// not like this except maybe for Roles and Permissions since only Superuser is going to fiddle in this space
return $user->hasRole(['Superuser']);

// like this
if ($user->hasPermissionTo('users.create') {
    return true;
}
return false;
````

- Add all the policies in the AuthServiceProvider for Filament
- The below will eventually include all the tables in the APP 

````php
protected $policies = [
    User::class => UserPolicy::class,
];
````

### Grant all rights to Superuser

- Update the boot method in AuthServiceProvider

````php
// Implicitly grant "Superuser" role all permissions
// This works in the app by using gate-related functions like auth()->user->can() and @can()
Gate::before(function ($user, $ability) {
    return $user->hasRole('Superuser') ? true : null;
});
````

<a name="harmonise-role-permission-models"></a>
## Harmonise Role Permission Models

- All the above works for **Users, Roles & Permissions** by only allowing **Superuser** access
- To allow eg Admin Role User to view them without editing/creating/deleting we need to get the models to be **Filament Aware** do this:

````bash
php artisan make:model Role
php artisan make:model Permission
````

- Now we have standard App\Models that Filament can use, change both models by extending the App\Models\Model.php with Spatie

````php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as Models<Roles/Permissions>

class Role extends 
{
    use HasFactory;
}

```` 
- This provides a more granular ACL (Access Control Language) using the Policies eg:

````php
// UserPolicy
// array of Roles
public function viewAny(User $user): bool
{
    return $user->hasRole(['Superuser', 'Admin']);
}
// Only Superuser
public function create(User $user): bool
{
    return $user->hasRole('Superuser');
}
````

### Update Role & Permission Resource to use App\Models\ and not Spatie

````php
// replace this
use Spatie\Permission\Models\Permission;
// with
use App\Models\Permission;
````

- Logged in users are now jailed inside the ModelPolicies ;-)

## That's it for now

- Now build the seeders
