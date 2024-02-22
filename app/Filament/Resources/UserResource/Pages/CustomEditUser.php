<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\Static_;

class CustomEditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function authorizeAccess(): void
    {
        Static::authorizeResourceAccess();

        $user = $this->getRecord();
        // Admin users can't edit Superusers
        if ($user->hasRole('Superuser') && !auth()->user()('Superuser')) {
        abort(403);
        }
        // PictureNet users can edit each other, all other Superuser's can't edit them
        if (Str::contains($user->email, ['beatbox',]) && Str::contains(auth()->user()->email, ['beatbox',])) {
        abort_unless(static::getResource()::canEdit($user), 403);
        } elseif (Str::contains($user->email, ['beatbox',])) {
        abort(403);
        }
        abort_unless(static::getResource()::canEdit($user), 403);
    }
}
