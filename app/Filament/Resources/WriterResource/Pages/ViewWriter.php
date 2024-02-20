<?php

namespace App\Filament\Resources\WriterResource\Pages;

use App\Filament\Resources\WriterResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWriter extends ViewRecord
{
    protected static string $resource = WriterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
