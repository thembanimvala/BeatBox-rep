# Protect User Edit & List to Guard SuperUsers

- If the auth()->user() does not have the Superuser Role they must not be able to edit a SuperUser
- Protect all Superusers that have **picturenet** in their emails from all users
- PictureNet users can each each other

## CustomEditUser

````bash
php artisan make:filament-page
````
- Page name: CustomEditUser
- Resource: UserResource
- Type: Edit
- Panel: Admin

````bash
INFO  Filament page [app/Filament/Admin/Resources/UserResource/Pages/CustomEditUser.php] created successfully.  

INFO  Make sure to register the page in `UserResource::getPages()`. 
````

- Copy the authorize function from custom extends Filament **EditRecord** and paste it into the CustomEditUser

````php
protected function authorizeAccess(): void
{
    static::authorizeResourceAccess();
 
    abort_unless(static::getResource()::canEdit($this->getRecord()), 403);
}
````

- Change the getPages() in the UserResource

````php
//            'edit' => UserResource\Pages\EditUser::route('/{record}/edit'),
            'edit' => UserResource\Pages\CustomUserEdit::route('/{record}/edit'),
````

**Test any user see it works as before** ie there is no Custom guard in place as yet

** Completed guard looks like this**

````php
protected function authorizeAccess(): void
{
    static::authorizeResourceAccess();

    $userRecord = $this->getRecord();

    // Admin users can't edit Superusers
    if ($userRecord->hasRole('Superuser') && !auth()->user()->hasRole('Superuser')) {
        abort(403);
    }

    // PictureNet users can edit each other, all other Superuser's can't edit them
    if (Str::contains($userRecord->email, ['picturenet', 'carsalesportal']) && Str::contains(auth()->user()->email, ['picturenet', 'carsalesportal'])) {
        abort_unless(static::getResource()::canEdit($userRecord), 403);
    } elseif (Str::contains($userRecord->email, ['picturenet', 'carsalesportal'])) {
        abort(403);
    }

    abort_unless(static::getResource()::canEdit($userRecord), 403);
}
````

## Catch the Delete User 

**UserResource**
- Remove delete the DeleteAction from the table

````php
Tables\Actions\DeleteAction::make(),
````

- Also Remove the bulkActions

````php
    ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
            Tables\Actions\DeleteBulkAction::make(),
        ]),
    ]);
````

- The EditUser logic already protects the PictureNet & Superuser so these users can not be edited
- The Delete is a modal and its not possible to url tamper to make the modal fire up
- We should be safe
- Simply add the Delete options to CustomEditUser

````php
protected function getHeaderActions(): array
{
    return [
        Actions\DeleteAction::make(),
        Actions\ForceDeleteAction::make(),
        Actions\RestoreAction::make(),
    ];
}
````

- Now other **non PictureNet users** can delete users only by editing them not from the list/table view
- Obviously the Roles & Permissions must be set up correctly re business logic 
