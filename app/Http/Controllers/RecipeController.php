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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use OpenApi\Annotations as OA;


/**
 * @OA\Info(
 *     title="RecipeBox API",
 *     version="1.0.0",
 *     description="API for managing recipes in RecipeBox"
 * )
 *
 *
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category",
 *     @OA\Property(property="id", type="integer", format="int64", description="Unique identifier for the category"),
 *     @OA\Property(property="name", type="string", maxLength=255, description="Name of the category")
 * )
 *
 * @OA\Schema(
 *     schema="Recipe",
 *     @OA\Property(property="id", type="integer", format="int64", description="Unique identifier for the recipe"),
 *     @OA\Property(property="user_id", type="integer", format="int64",
 *     description="Foreign key referencing the user who created the recipe"),
 *     @OA\Property(property="title", type="string", maxLength=255, description="Title of the recipe"),
 *     @OA\Property(property="description", type="string", description="Description of the recipe"),
 *     @OA\Property(property="ingredients", type="string", description="Ingredients used in the recipe"),
 *     @OA\Property(property="instructions", type="string", description="Instructions for cooking the recipe"),
 *     @OA\Property(property="image", type="string", maxLength=2048,
 *     description="URL of an image associated with the recipe"),
 *     @OA\Property(property="created_at", type="string", format="date-time",
 *     description="Timestamp of when the recipe was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time",
 *     description="Timestamp of when the recipe was last updated")
 * )
 *
 * @OA\Schema(
 *     schema="RecipeWithCategories",
 *     type="object",
 *     title="RecipeWithCategories",
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="ingredients", type="string"),
 *     @OA\Property(property="instructions", type="string"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Category")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="StoreRecipeRequest",
 *     required={"title"},
 *     @OA\Property(property="title", type="string", example="Spaghetti Bolognese"),
 *     @OA\Property(property="description", type="string", example=""),
 *     @OA\Property(property="ingredients", type="string",
 *     example="Spaghetti, minced beef, onion, garlic, tomato sauce"),
 *     @OA\Property(property="instructions", type="string",
 *     example="1. Cook spaghetti. 2. Fry minced beef, onion and garlic. 3. Add tomato sauce.
 * 4. Mix with cooked spaghetti."),
 *     @OA\Property(property="category_ids", type="array", @OA\Items(type="integer", example=1))
 * )
 *
 * @OA\Schema(
 *     schema="UpdateRecipeRequest",
 *     title="Update Recipe Request",
 *     description="Request body parameters for updating a recipe.",
 *     type="object",
 *     required={"title"},
 *     @OA\Property(property="title", type="string", maxLength=255),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="ingredients", type="string"),
 *     @OA\Property(property="instructions", type="string"),
 *     @OA\Property(property="image", type="string", format="binary"),
 *     @OA\Property(
 *         property="category_ids",
 *         type="array",
 *         @OA\Items(type="integer", example=1)
 *     )
 * )
 *
 *  * @OA\Schema(
 *     schema="RecipeListResponse",
 *     type="object",
 *     title="Recipe List Response",
 *     @OA\Property(property="current_page", type="integer"),
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/RecipeWithCategories")),
 *     @OA\Property(property="first_page_url", type="string"),
 *     @OA\Property(property="from", type="integer"),
 *     @OA\Property(property="last_page", type="integer"),
 *     @OA\Property(property="last_page_url", type="string"),
 *     @OA\Property(
 *         property="links",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="url", type="string"),
 *             @OA\Property(property="label", type="string"),
 *             @OA\Property(property="active", type="boolean")
 *         )
 *     ),
 *     @OA\Property(property="next_page_url", type="string", nullable=true),
 *     @OA\Property(property="path", type="string"),
 *     @OA\Property(property="per_page", type="integer"),
 *     @OA\Property(property="prev_page_url", type="string", nullable=true),
 *     @OA\Property(property="to", type="integer"),
 *     @OA\Property(property="total", type="integer")
 * )
 *
 * @OA\Schema(
 *     schema="ErrorUnauthorized",
 *     title="Unauthorized Error",
 *     description="The request requires authentication,
 * and the user is not authenticated or authenticated incorrectly.",
 *     type="object",
 *     required={"message"},
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Unauthorized"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ErrorForbidden",
 *     title="Forbidden Error",
 *     description="The server understood the request but refuses to authorize it.",
 *     type="object",
 *     required={"message"},
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Forbidden"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ErrorNotFound",
 *     title="Not Found Error",
 *     description="The requested resource could not be found but may be available in the future.",
 *     type="object",
 *     required={"message"},
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="No query results for model [App\Models\{ModelName}] {id}"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ErrorValidation",
 *     title="Validation Error",
 *     description="The request data failed validation.",
 *     type="object",
 *     required={"message", "errors"},
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="The given data was invalid."
 *     ),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         additionalProperties={
 *             "type": "array",
 *             "items": {
 *                 "type": "string"
 *             }
 *         }
 *     )
 * )
 */
class RecipeController extends Controller
{
    public const PATH_TO_IMAGE = 'public/images/recipes';

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            /**
             * transform null to ''
             *
             * JS problem:
             * FormData.append(<key>, '') transform '' to null
             */

            $input = array_map(static function ($value) {
                return is_null($value) ? '' : $value;
            }, $request->all());

            $request->replace($input);

            return $next($request);
        })->only(['store', 'update']);
    }

    /**
     * @OA\Get(
     *     path="/recipes/searchTypes",
     *     summary="Get search types",
     *     description="Get a list of available search types for recipes.",
     *     tags={"Recipes"},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="string",
     *                 example="name"
     *             )
     *         )
     *     )
     * )
     * @return JsonResponse
     */
    public function getSearchTypes(): JsonResponse
    {
        return response()->json(SearchTypeEnum::getValues());
    }

    /**
     * Display a listing of the resource.
     * @OA\Get(
     *     path="/recipes",
     *     tags={"Recipes"},
     *     summary="Get a paginated list of all recipes, optionally filtered by keyword, category IDs,
     * and search type.",
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Keyword to search recipes by.",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="category_ids",
     *         in="query",
     *         description="Array of category IDs to filter recipes by.",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(
     *                 type="integer"
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="searchType",
     *         in="query",
     *         description="Search type to use (simple, with_ingridients or deep).",
     *         @OA\Schema(
     *             type="string",
     *             enum={"simple", "with_ingridients", "deep"},
     *             default="simple"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/RecipeWithCategories")
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidEnumMemberException
     */
    public function index(Request $request): JsonResponse
    {
        // Get the query parameters from the request
        $keyword = $request->query('keyword');
        $categoryIds = $request->query('category_ids');
        $searchType = new SearchTypeEnum($request->input('searchType', SearchTypeEnum::SIMPLE));

        $query = Recipe::query();

        $this->filterByKeywordAndCategoryId($query, $keyword, $searchType, $categoryIds);

        $recipes = $query->with('categories')->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($recipes);
    }

    /**
     * @param Builder $query
     * @param string|null $keyword
     * @param SearchTypeEnum $searchType
     * @param array|null $categoryIds
     */
    private function filterByKeywordAndCategoryId(
        Builder $query,
        ?string $keyword,
        SearchTypeEnum $searchType,
        ?array $categoryIds
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
        if ($categoryIds) {
            $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('id', $categoryIds);
            });
        }
    }


    /**
     * Store a newly created resource in storage.
     * @OA\Post(
     *     path="/recipes",
     *     summary="Create a new recipe",
     *     description="Create a new recipe.",
     *     tags={"Recipes"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body containing recipe data.",
     *         @OA\JsonContent(ref="#/components/schemas/StoreRecipeRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/RecipeWithCategories")
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorUnauthorized")
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="Forbidden",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorForbidden")
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorValidation")
     *     )
     * )
     * @param StoreRecipeRequest $request
     * @return JsonResponse
     */
    public function store(StoreRecipeRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            /** @noinspection NullPointerExceptionInspection */
            $path = $request->file('image')->store(static::PATH_TO_IMAGE);
            $validated['image'] = $path;
        }

        $recipe = $request->user()->recipes()->create($validated);

        if (!empty($validated['category_ids'])) {
            $recipe->categories()->sync($validated['category_ids']);
            $recipe->load(['categories' => function ($query) {
                $query->select('id');
            }]);
        }

        return response()->json($recipe, 201);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/recipes/{recipe}",
     *     summary="Get a recipe",
     *     description="Retrieve a recipe by ID.",
     *     tags={"Recipes"},
     *     @OA\Parameter(
     *         name="recipe",
     *         in="path",
     *         description="ID of recipe to retrieve.",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="The requested recipe",
     *         @OA\JsonContent(ref="#/components/schemas/RecipeWithCategories")
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not Found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorNotFound")
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
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
     * @OA\Put(
     *     path="/recipes/{recipeId}",
     *     operationId="updateRecipe",
     *     summary="Update a recipe",
     *     description="Update a recipe with the given ID.",
     *     tags={"Recipes"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="recipeId",
     *         in="path",
     *         description="ID of recipe to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Recipe object that needs to be updated",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateRecipeRequest")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Recipe updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/RecipeWithCategories")
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorUnauthorized")
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="Forbidden",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorForbidden")
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not Found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorNotFound")
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorValidation")
     *     )
     * )
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
        $recipe->categories()->sync($request->input('category_ids'));

        return response()->json($recipe);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/recipes/{recipe}",
     *     tags={"Recipes"},
     *     summary="Delete a recipe",
     *     description="Delete the specified recipe if the authenticated user is the owner.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="recipe",
     *         in="path",
     *         description="ID of the recipe to delete.",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="The recipe was deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Recipe deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="Forbidden",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorForbidden")
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not Found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorNotFound")
     *     ),
     * )
     *
     * @param Recipe $recipe
     * @return JsonResponse
     */
    public function destroy(Recipe $recipe): JsonResponse
    {
        if (Gate::denies('delete-recipe', $recipe)) {
            abort(403, "This action is unauthorized.");
        }

        $recipe->delete();
        return response()->json(['message' => 'Recipe deleted']);
    }
}
