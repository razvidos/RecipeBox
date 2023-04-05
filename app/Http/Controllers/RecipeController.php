<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $recipes = Recipe::orderBy('created_at', 'desc')->paginate(10);
        return response()->json($recipes);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRecipeRequest $request
     * @return JsonResponse
     */
    public function store(StoreRecipeRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $recipe = $request->user()->recipes()->create($validated);
        $recipe->categories()->sync($validated->categories);

        return response()->json($recipe);
    }

    /**
     * Display the specified resource.
     *
     * @param Recipe $recipe
     * @return JsonResponse
     */
    public function show(Recipe $recipe): JsonResponse
    {
        return response()->json($recipe);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRecipeRequest $request
     * @param Recipe $recipe
     * @return JsonResponse
     */
    public function update(UpdateRecipeRequest $request, Recipe $recipe): JsonResponse
    {
        $validated = $request->validated();

        $recipe->update($validated);
        $recipe->categories()->sync($validated->categories);

        return response()->json($recipe);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Recipe $recipe
     * @return JsonResponse
     */
    public function destroy(Recipe $recipe): JsonResponse
    {
        if (Gate::denies('recipe-book')) {
            abort(403);
        }
        $recipe->delete();
        return response()->json(['message' => 'Recipe deleted']);
    }
}
