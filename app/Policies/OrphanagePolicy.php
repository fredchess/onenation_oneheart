<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\Orphanage;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrphanagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Orphanage $orphanage): bool
    {
        return true;
        return $user->hasRole([UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value]) || $user->id == $orphanage->responsable_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole([UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Orphanage $orphanage): bool
    {
        return $user->hasRole([UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value]) || $user->id == $orphanage->responsable_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Orphanage $orphanage): bool
    {
        return $user->hasRole([UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Orphanage $orphanage): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Orphanage $orphanage): bool
    {
        return false;
    }
}
