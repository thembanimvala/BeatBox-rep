# Filament Table Tabs

- Edit the Resource\Pages\ListUsers.php
- Add a getTabs() function to it

````php
public function getTabs(): array
{
    return [
        'All' => Tab::make(),
        'This Week' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subWeek()))
            ->badge(User::query()->where('created_at', '>=', now()->subWeek())->count())->badgeColor('success'),
        'This Month' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subMonth()))
            ->badge(User::query()->where('created_at', '>=', now()->subMonth())->count())->badgeColor('info'),
        'This Year' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subYear()))
            ->badge(User::query()->where('created_at', '>=', now()->subYear())->count())->badgeColor('danger'),
    ];
}
````
