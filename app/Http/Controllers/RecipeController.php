<?php

namespace App\Http\Controllers;

use App\Enums\SearchTypeEnum;
use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Models\Recipe;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;


class RecipeController extends Controller
{
    public const PATH_TO_IMAGE = 'public/images/recipes';

    public function getSearchTypes(): JsonResponse
    {
        return response()->json(SearchTypeEnum::getValues());
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidEnumMemberException
     */
    public function index(Request $request): JsonResponse
    {
        DB::enableQueryLog();

        // Get the query parameters from the request
        $keyword = $request->query('keyword');
        $category_ids = $request->query('category_ids');
        $searchType = new SearchTypeEnum($request->query('searchType'));

        $query = Recipe::query();

        $this->filterByKeywordAndCategoryId($query, $keyword, $searchType, $category_ids);

        $recipes = $query->with('categories')->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($recipes);
    }

    /**
     * @param Builder $query
     * @param string|null $keyword
     * @param SearchTypeEnum $searchType
     * @param array|null $category_ids
     */
    private function filterByKeywordAndCategoryId(
        Builder $query,
        ?string $keyword,
        SearchTypeEnum $searchType,
        ?array $category_ids
    ): void
    {
        // filter by keyword in recipe table
        if ($keyword) {

            if ($searchType->is(SearchTypeEnum::WITH_INGREDIENTS)) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('ingredients', 'like', "%{$keyword}%");
                });
            }

            if ($searchType->is(SearchTypeEnum::DEEP)) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('ingredients', 'like', "%{$keyword}%")
                        ->orWhere('instructions', 'like', "%{$keyword}%");
                });
            }

            if ($searchType->is(SearchTypeEnum::SIMPLE)) {
                $query->where('title', 'like', "%$keyword%");
            }
        }

        // filter by category_id
        if ($category_ids) {
            $query->whereHas('categories', function ($q) use ($category_ids) {
                $q->whereIn('id', $category_ids);
            });
        }
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

        /**
         * transform null to ''
         *
         * JS problem:
         * FormData.append(<key>, '') transform '' to null
         */
        $validated = array_map(static function ($value) {
            return is_null($value) ? '' : $value;
        }, $validated);

        if ($request->hasFile('image')) {
            /** @noinspection NullPointerExceptionInspection */
            $path = $request->file('image')->store(static::PATH_TO_IMAGE);
            $validated['image'] = $path;
        }

        $recipe = $request->user()->recipes()->create($validated);


        if (!empty($validated['category_ids'])) {
            $recipe->categories()->sync($validated['category_ids']);
        }

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
        if (str_starts_with($recipe->image, 'public/')) {
            $recipe->image = Storage::url($recipe->image);
        }
        $recipe->load('categories');
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

        if ($request->hasFile('image')) {
            /** @noinspection NullPointerExceptionInspection */
            $path = $request->file('image')->store(static::PATH_TO_IMAGE);
            $validated['image'] = $path;
        }

        $recipe->update($validated);
        $recipe->categories()->sync($validated['category_ids']);

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
        if (Gate::denies('recipe-book', $recipe)) {
            abort(403);
        }
        $recipe->delete();
        return response()->json(['message' => 'Recipe deleted']);
    }
}
