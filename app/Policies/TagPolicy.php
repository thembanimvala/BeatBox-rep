<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TagPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('Superuser')){
            return true;
        }
        return $user->can('tags.list');
    }
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tag $tag): bool
    {
        return $user->can('tags.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Tag $tag): bool
    {
        return $user->can('tags.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tag $tag): bool
    {
        return $user->can('tags.update');
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tag $tag): bool
    {
        return $user->can('tags.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tag $tag): bool
    {
        return $user->can('tags.restore');
    }
    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forcedelete(User $user, Tag $tag): bool
    {
        return $user->can('tags.destroy');
    }
}
