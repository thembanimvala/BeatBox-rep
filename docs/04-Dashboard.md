## Dashboard tweaks

- AdminPanelProvider.php inside App\Providers\Filament;
- Simple update to extend the colours

````php
->colors([
    'danger' => Color::Rose,
    'gray' => Color::Gray,
    'info' => Color::Blue,
    'primary' => Color::Indigo,
    'success' => Color::Emerald,
    'warning' => Color::Orange,
])
->font('Inter')     
````

### Collapse Sidebar & max width

- Update Filament in config file

````php
'layout' => [
    'sidebar' => [
        'is_collapsible_on_desktop' => true,
    ],
    'max_content_width' => 'full',
],
````

- Add to PanelProvider

````php
->sidebarCollapsibleOnDesktop()

//   ->maxContentWidth(MaxWidth::Full);
````

#### Max width

- Add to PanelProvider then all of "admin" will be max width look above
- Custom max for each page then add to each page not the Resource
- Create, Edit, List etc

````php
protected ?string $maxContentWidth = 'full';
````

### Change sidebar menu icons

- In ModelResource

````php
protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
protected static ?string $activeNavigationIcon = 'heroicon-o-document-text';
````

### Resource Generator

- Since we got the tables we can use --generate to auto fill the Resource for us
- Include the --view so that we have the view component to work with our permissions aka view only

````bash
php artisan make:filament-resource Province --generate --view
php artisan make:filament-resource City --generate --view
php artisan make:filament-resource Owner --generate --view
php artisan make:filament-resource Store --generate --view
````

- To auto generate/update slugs edit the Form in the Resource

````php
TextInput::make('name')
    ->required()
    ->reactive()
    ->debounce(600)
    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
TextInput::make('slug'),
````

## Sidebar Badges with count

- Add to the Resource file

````php
public static function getNavigationBadge(): ?string
{
    return static::getModel()::count();
}

public static function getNavigationBadgeColor(): string|array|null
{
    return static::getModel()::count() > 10 ? 'warning' : 'success';
}
````

## Global Search

- In the Panel Resource files which must be included in the search add these
  - Below is the StoreResource example

````php
protected static ?string $recordTitleAttribute = 'name';

public static  function getGloballySearchableAttributes(): array
{
    return ['name', 'email', 'phone', 'contact', 'owner.name', 'province.name', 'city.name'];
}

public static function getGlobalSearchResultTitle(Model $record): string
{
    return $record->name;
}
public static function getGlobalSearchResultDetails(Model $record): array
{
    return [
        'Manager' => $record->contact,
        'City' => $record->city->name,
        'Phone' => $record->phone,
        'Owner' => $record->owner->name
    ];
}
````

- Better speed using Eloquent vs using Lazy Loading above
- Add this function for the linked models

````php
public static function getGlobalSearchEloquentQuery(): Builder
{
    return parent::getGlobalSearchEloquentQuery()->with(['province', 'city', 'owner']);
}
````
