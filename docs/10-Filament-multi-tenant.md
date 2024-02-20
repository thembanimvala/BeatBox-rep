# [Filament Multi Tenant](https://filamentphp.com/docs/3.x/panels/tenancy)

By now there is fully functioning admin and an app panel with users, role, permissions and user activities.

Now extend the application to handle each store as a Tenant, implies each stores staff etc is stored in the dbase with a store_id.

> The term "multi-tenancy" is broad and may mean different things in different contexts. Filament's tenancy system implies that the user belongs to many tenants (organizations, teams, companies, etc.) and may switch between them.

- All models that hold Store records must have a store_id
- When migrating new models make sure you enter a store_id
- To simulate this create a Staff model for people who work in a Store
- For now add this to the AdminPanel
- Add activities to the panel and also Staff to the PermissionSeeder and then faker some staff into the stores
- We also need Superuser & Admin users to belong to all the stores

## Add a store_user pivot table to the database

````bash
php artisan make:migration create_store_user
````

````php
Schema::create('store_user', function (Blueprint $table) {
    $table->id();
    $table->foreignId('store_id')->constrained();
    $table->foreignId('user_id')->constrained();
});
````
- Rerun the migration

````bash
php artisan migrate:fresh --seed
````

- Nothing visibly changed  only in MySQL

## Update the User Model in preparation for the Filament Multi Tenancy

- Replace team below with stores and include the HasTenants

````php
use Filament\Models\Contracts\HasTenants;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    public function getTenants(Panel $panel): Collection
    {
        return $this->teams;
    }
    
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }
    
    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams->contains($tenant);
    }
````

### To attach a user to a model 
> 
> The juice lines are 
> 
> $store->members()->attach(auth()->user());
> or
> $user->stores()->attach($storeIds);

## Retro fit Superuser and Admin users to all stores

- Add a MultiTenancySeeder don't forget to add it to the DatabaseSeeder after ApplicationSeeder

````php
public function run(): void
{
    $users = User::all();

    $storeIds = Store::pluck('id');

    foreach($users as $user) {
        if ($user->canAccessAdminPanel()) {
            $user->stores()->attach($storeIds);
        }
    }
}
````

- You can test this seeder only vs the full refresh --seed

````php
php artisan db:seed --class=MultiTenantSeeder
````

## Add Filament Multi-tenancy to the App Panel

````php
->tenant(Store::class, ownershipRelationship: 'store', slugAttribute: 'slug');
// ->tenantRoutePrefix('store');
````

- Nothing mush to see as yet add a RelationManager to Users to see the Stores neatly and manage the User to Store relationship


