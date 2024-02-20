# Filament Plugin Activity Log

- [Dennis Koch's Plugin](https://filamentphp.com/plugins/pxlrbt-activity-log)

1. Install, migrate & publish Spatie Activity Log
2. Install & publish Filament Activity Log
3. Create a Filament page to display the activity
4. Update all the Models that must be logged 
5. Add the action & pages to the PanelModelResource
6. We created a Custom Filaments/Components/Pages/ListActivities.
7. Extend the permissions to include activities
8. Catch/edit the restore ability in the custom ListActivities

## Install

````bash
composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate:status
php artisan migrate
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-config"

# now install Dennis Koch's Filament Plugin
composer require pxlrbt/filament-activity-log
php artisan vendor:publish      # search for 'pxlrbt' and publish
# or you can call it directly 
php artisan vendor:publish --"provider=pxlrbt\FilamentActivityLog\FilamentActivityLogServiceProvider"
````

## Create a Filament Page

````bash
# all in one line
php artisan make:filament-page ListUserActivities --resource=UserResource --type=custom
# or
php artisan make:filament-page          # follow prompts
````

## Update the Models

- Do this to all the models you want to log 

````php
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

// Inside the class
use LogsActivity;

// add function
public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logOnlyDirty()
        ->logOnly(['*']);
}
````

- The logOnlyDirty stores the changed fields
- The wildcard above will log all the fields in the model or you could supply the fields to only log the ones in it

## Add actions ModelResource

````php
Action::make('activities')->url(fn ($record) => UserResource::getUrl('activities', ['record' => $record])),
````

## Add Page to ModelResource

````php

return [
    'index' => UserResource\Pages\ListUsers::route('/'),
    'create' => UserResource\Pages\CreateUser::route('/create'),
    'edit' => UserResource\Pages\EditUser::route('/{record}/edit'),
    'activities' => UserResource\Pages\ListUserActivities::route('/{record}/activities'),
];
````

## Custom ListActivites Component

- This is where we can add our own policy logic and not break the APP if & when Dennis upgrades his plugin hopefully

````php
namespace App\Filament\Components\Pages;

use App\Filament\Admin\Resources\UserResource\Pages\Exception;
use Filament\Notifications\Notification;
use pxlrbt\FilamentActivityLog\Pages\ListActivities as MainListActivities;

class ListActivities extends MainListActivities
{
    public function mount($record)
    {
        $this->authorizeAccess();

        $this->record = $this->resolveRecord($record);
    }

    public function restoreActivity(int|string $key)
    {
        if (! static::getResource()::canRestore($this->record)) {
            abort(403);
        }

        $activity = $this->record->activities()
            ->whereKey($key)
            ->first();

        // If the user owns the activity ie they updated the model then they can restore it!!! 
        // Superuser can do everything
        if (auth()->user()->can('restore', $activity)) {
            abort(403);
        }

        $oldProperties = data_get($activity, 'properties.old');

        if ($oldProperties === null) {
            Notification::make()
                ->title(__('filament-activity-log::activities.events.restore_failed'))
                ->danger()
                ->send();

            return;
        }

        try {
            $this->record->update($oldProperties);

            Notification::make()
                ->title(__('filament-activity-log::activities.events.restore_successful'))
                ->success()
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->title(__('filament-activity-log::activities.events.restore_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
````

## Edit the ListUserActivities Page

**Note**
- The Model 'User::class' is here and will be different in all Activity pages
- We already have a view chances are we don't need to create more we can reuse the same one below
- **can('activities', self::$model::find($this->record))** is the magic juice
- Must have 'activities' in all model policies

````php
namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Filament\Components\Pages\ListActivities;
use App\Models\User;

class ListUserActivities extends ListActivities
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.admin.resources.user-resource.pages.list-user-activities';

    protected static string $model = User::class;

    protected function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        abort_unless(static::getResource()::can('activities', self::$model::find($this->record)), 403);
    }
}
````

## Model Policy

- Dennis's Restore reads the Model restore policy aka softdeletes() 
- Not ideal, for we now left that as is
- In order to display the Restore button and have it actually do the Restore - ModelPolicy restore above needed
- The new permission we added to handle the display/view/index of the activities below

````php
public function activities(User $user, User $model): bool
{
    return $user->hasPermissionTo('users.activities');
}
````



- To clear the logs use spatie command

````bash
php artisan activitylog:clean --days=1 --force
php artisan activitylog:clean --days=180 --force
````
