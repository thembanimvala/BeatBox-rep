# Filament Relationships

- Display Cities under a Province

````bash
# 3 x inputs
# Resource class for owner model eg ProvinceResource
# Relationship inside the Province model eg cities
# Attribute to identify the city eg name    # aka cities.name
php artisan make:filament-relation-manager ProvinceResource cities name

# CitiesRelationManager.php  created successfully
# Make sure to register the relation in `ProvinceResource::getRelations()`
````

## Update the parent Model

````php
public function cities(): HasMany
{
    return $this->hasMany(City::class);
}
````

## Add this to the Panel at the top for namespacing etc

````php
use App\Filament\Admin\Resources;
````

- Update the form in the Resource file to include more fields in the modal edit

````php
public static function getRelations(): array
{
    return [
        Resources\ProvinceResource\RelationManagers\CitiesRelationManager::class
    ];
}
````

- Edit the RelationManagers file to extend the table columns/usability ie add ->searchable() 

## Disable a tenancy for a Resource

````php
protected static bool $isScopedToTenant = false;
````
