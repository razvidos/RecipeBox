<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        $recipes = $user->recipes()->orderByDesc('created_at')->get();
        $user = $user->id === auth()->user()->id ? $user : ['name' => $user->name];

        return response()->json([
            'user' => $user,
            'recipes' => $recipes,
        ]);
    }
}
