<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Tabs\Tab as TabsTab;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Resources\Components\Tab as ComponentsTab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
    protected ?string $maxContentWidth = 'full';
    protected function getHeaderActions(): array

    {
        return [
            Actions\CreateAction::make(),
        ];
    }

        public function getTabs(): array
    {

        return [
            'All' => ComponentsTab::make('All')
                ->modifyQueryUsing(fn (Builder $query) => $query)
                ->badge(User::query()->count())->badgeColor('success'),
            'Active' => ComponentsTab::make('Active')
              ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', 1))
              ->badge(User::query()->where('is_active', '>=', now()->subWeek())->count())->badgeColor('success'),
            'This Week' => ComponentsTab::make('This week')
              ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subWeek()))
              ->badge(User::query()->where('created_at', '>=', now()->subWeek())->count())->badgeColor('success'),
            'This Month' => ComponentsTab::make('This Month')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subMonth()))
                ->badge(User::query()->where('created_at', '>=', now()->subMonth())->count())->badgeColor('info'),
            'This Year' => ComponentsTab::make('This Year')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subYear()))
                ->badge(User::query()->where('created_at', '>=', now()->subYear())->count())->badgeColor('danger'),
        ];
    }
}
