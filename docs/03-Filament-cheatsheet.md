## Filament CheatSheet


- To enable **softdeletes** on models and resources you must generate the resource with the option flag

````bash
php artisan make:filament-resource City --soft-deletes
- for activity
 php artisan make:filament-resource Blog --generate --view --soft-deletes
````

- Obviously you need to include the option in the migration

````php
$table->softDeletes();
````

- Also add it to the model

````php
use SoftDeletes;

  protected $fillable =[
        'name',
        'writer_id',
        'slug',
        'intro',
        'content',
        'photo',
    ];
````

- Add it to the filters in the ModelResource

````php
->filters([
    Tables\Filters\TrashedFilter::make(),
])
````

- Add the restore TODO

## Add userProfile edit name email password

- In the Panel chain the method
- For now only do this in the AdminPanel ie trusted users

````php
->path('admin')
->profile()
````



## Delete old image at edit

- Add to the Model

````php
protected static function boot()
{
    parent::boot();

    /** @var Model $model */
    static::updating(function ($model) {
        if ($model->isDirty('image') && ($model->getOriginal('image') !== null)) {
            Storage::disk('public')->delete($model->getOriginal('image'));
        }
    });
}
````


## Custom Table Column

````bash
php artisan make:table-column
````

- Stored in app/Tables/Columns/
- In the Resource you now call this Colum in place of the TextColumn eg

````php
UserName::make('user.name')->label('Created by'),
````

- In the blade you have access to the {{ dump(getRecord()) }}
- Now you can do your own custom query and more
- [Dan Harrin creator of Filament](https://laracasts.com/series/build-advanced-components-for-filament/episodes/10) 

## Customising the From layout grid

- [Trevor Morris](https://www.trovster.com/blog/2023/09/organising-filament-form-sections) 

## Export to Excel

- [Dennis Koch](https://filamentphp.com/plugins/pxlrbt-excel#installation) core-team member of Filament

## Table tricks

````php
// Column
->searchable(isIndividual: true)
->hidden(auth()->user()->email == 'admin@csp')
->visible(auth()->user()->email == 'admin@csp')
````

- To do counts there must be a relationship (BelongsToMany) in the model

````php
// ProvinceResource
Tables\Columns\TextColumn::make('cities_count')
    ->label('Cities')
    ->alignCenter(true)
    ->counts('cities'),
````

### Add Searchable Filters to a Form field

````php
// UserReource
Select::make('role')
    ->multiple()
    ->searchable()
    ->getSearchResultsUsing(fn (string $search): array => Role::where('name', 'like', "%$search%")->limit(50)->pluck('name', 'id')->toArray())
    ->getOptionLabelsUsing(fn (array $values): array => Role::whereIn('id', $values)->pluck('name', 'id')->toArray())
    ->relationship('roles', 'name')
    ->preload(),
````

- For more complicated filter check the UserResource where we pull in  reusable Component DateRange filter
- Pay attention to the use statements needed to get it working

### Add Tabs above the table

- In the pages directory edit ListUsers

````php
public function getTabs(): array
{
    return [
        'All' => Tab::make(),
        'This Week' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subWeek()))
            ->badge(User::query()->where('created_at', '>=', now()->subWeek())->count()),
        'This Month' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subMonth()))
            ->badge(User::query()->where('created_at', '>=', now()->subMonth())->count()),
        'This Year' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subYear()))
            ->badge(User::query()->where('created_at', '>=', now()->subYear())->count()),
    ];
}
````
