# Filament Additional Panels

- The default admin panel is backoffice now we need another Panel/url/route for the staff

````bash
php artisan make:filament-panel app
````

- Look inside the **AppPanelProvider** and note that the namespace routes are not the same as the AdminPanel
- When creating a Resource for this panel Filament will now prompt you for which panel

````bash
php artisan make:filament-resource Store --soft-deletes --view --generate
````

- Choose the App panel we include the --view to work with our Permissions for users who only have view permissions
- Check the Panel files note the 'default' setting on the admin panel

````php
return $panel
    ->default()
    ->id('admin')
    ->path('admin')
    ->login()
````

## Remove the login from the Admin panel completely everyone logs in via app

- From AdminPanel remove default() & login() and ->authMiddleware()

````php
->authMiddleware([
    Authenticate::class,
]);
```` 

- **NOTE** Make sure to add the ->login() chained method to the new App Panel

### Switch between panels 

- Add under userMenuItems
- Do similar for the App Panel 

````php
->login()
->userMenuItems([
    MenuItem::make()
        ->label('Admin Area')
        ->icon('heroicon-o-building-storefront')
        ->url('/admin')
        ->visible(fn (): bool => auth()->user()->canAccessAdminPanel())
])
````

- Add to the User model

````php
public function canAccessAdminPanel(): bool
{
    return $this->hasRole(['Superuser', 'Admin', 'Manager']);
}
````



- Do the same in the AdminPanel to get to the AppPanel


## Harmonize Directory Structures & Planning

- The default Filament resource structure is \App\Filament
- The config/provider is \App\Providers\Filament
- In the providers there is a file for each Panel
- When adding a 2nd panel 
  - A new directory is created for the panel \App\Filament\App
  - A new file AppPanelProvider is registered in the config/app.php
  
````php
App\Providers\Filament\AdminPanelProvider::class,
App\Providers\Filament\AppPanelProvider::class,
````

- To harmonize up the directory structure we going to edit the default 'Admin' Provider so that we have Panels inside App\Filament ie move the Resources below into an Admin directory
- You need to copy the Admin panel and pages into a directory **admin** which you create on the same level as the new App panel

#### Before

```
├── App                             # the new App panel the Admin Panel contents below
├── CityResource
├── OwnerResource
├── PermissionResource
├── ProvinceResource
├── RoleResource
├── StoreResource
├── UserResource
├── CityResource.php
├── OwnerResource.php
├── PermissionResource.php
├── ProvinceResource.php
├── RoleResource.php
├── StoreResource.php
└── UserResource.php   
```

- app\Providers\Filament\AdminPanelProvider.php

````php
->discoverResources(in: app_path('Filament/'), for: 'App\\Filament')
->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
````

- app\Filament\UserResource.php

````php
'index' => Resources\UserResource\Pages\ListUsers::route('/'),
'create' => Resources\UserResource\Pages\CreateUser::route('/create'),
'edit' => Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
````

#### After

````php
->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
````

````php
'index' => UserResource\Pages\ListUsers::route('/'),
'create' => UserResource\Pages\CreateUser::route('/create'),
'edit' => UserResource\Pages\EditUser::route('/{record}/edit'),
````

```
├── Admin                           # The default Filament 1st panel 
│ └── Resources                         # We moved the above in here
├── App                             # our 2nd panel
│ ├── Components                        # Specific files for this panel
│ ├── Resources                         # The generated resources for this panel
│ └── Widgets                           # Widgets for this panel
└── Components                      # DRY (Don't Repeat Yourself)
    ├── Fields                          # reusable snippets to use in both panels above
    ├── Forms
    └── Tables
```

