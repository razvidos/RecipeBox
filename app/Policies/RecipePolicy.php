<?php

namespace App\Policies;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class RecipePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can store the model.
     *
     * @param User $user
     * @param Recipe $recipe
     * @return Response|bool
     */
    public function store(User $user, Recipe $recipe): Response|bool
    {
        return $user->id === $recipe->user_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Recipe $recipe
     * @return Response|bool
     */
    public function update(User $user, Recipe $recipe): Response|bool
    {
        return $user->id === $recipe->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Recipe $recipe
     * @return Response|bool
     */
    public function delete(User $user, Recipe $recipe): Response|bool
    {
        return $user->id === $recipe->user_id;
    }
}
