<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;


class CategoryController extends Controller
{

    /**
     * Get a list of all categories.
     * @OA\Get(
     *      path="/api/categoryList",
     *      operationId="getCategories",
     *      tags={"Categories"},
     *      summary="Get a list of all categories",
     *      description="Returns a list of all categories, sorted alphabetically by name.",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="Category A")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthorized.")
     *          )
     *      )
     * )
     *
     * @return JsonResponse
     */
    public function getCategories(): JsonResponse
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return response()->json($categories);
    }
}
