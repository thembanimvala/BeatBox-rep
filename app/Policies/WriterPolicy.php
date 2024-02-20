<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Writer;
use Illuminate\Auth\Access\Response;

class WriterPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('Superuser')){
            return true;
        }
        return $user->can('writers.list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Writer $writer): bool
    {
        return $user->can('writers.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('writers.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Writer $writer): bool
    {
        return $user->can('writers.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Writer $writer): bool
    {
        return $user->can('writers.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Writer $writer): bool
    {
        return $user->can('writers.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Writer $writer): bool
    {
        return $user->can('writers.destroy');
    }
}
