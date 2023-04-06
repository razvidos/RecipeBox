<?php

namespace App\Policies;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecipePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can store the model.
     *
     * @param User $user
     * @return bool
     */
    public function store(User $user): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Recipe $recipe
     * @return bool
     */
    public function update(User $user, Recipe $recipe): bool
    {
        return $user && $user->id === $recipe->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Recipe $recipe
     * @return bool
     */
    public function delete(User $user, Recipe $recipe): bool
    {
        return $user && $user->id === $recipe->user_id;
    }
}
