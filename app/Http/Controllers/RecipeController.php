<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RecipeController extends Controller
{
    public const SEARCH_TYPE_SIMPLE = 'simple';
    public const SEARCH_TYPE_WITH_INGREDIENTS = 'with_ingredients';
    public const SEARCH_TYPE_DEEP = 'deep';


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Get the query parameters from the request
        $keyword = $request->query('keyword');
        $category_id = $request->query('category_id');

        $query = Recipe::query();

        // filter by keyword in recipe table
        if ($keyword) {
            $searchType = $request->query('searchType');

            $query->where('title', 'like', "%$keyword%");

            if (static::SEARCH_TYPE_WITH_INGREDIENTS === $searchType) {
                $query->orWhere('ingredients', 'like', "%{$keyword}%");
            }

            if (static::SEARCH_TYPE_DEEP === $searchType) {
                $query->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('ingredients', 'like', "%{$keyword}%")
                    ->orWhere('instructions', 'like', "%{$keyword}%");
            }

        }

        // filter by category_id
        if ($category_id) {
            $query->whereHas('categories', function ($q) use ($category_id) {
                $q->where('id', $category_id);
            });
        }

        $recipes = $query->orderBy('created_at', 'desc')->paginate(10);

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
