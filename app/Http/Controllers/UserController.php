<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users/{user}",
     *     operationId="getUser",
     *     tags={"Users"},
     *     summary="Get user and their recipes",
     *     description="Returns a user object with their name and a list of their recipes sorted by creation date.",
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="The ID of the user to retrieve.",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1
     *         )
     *     ),
     * @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(
     *         @OA\Property(
     *             property="user",
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=2),
     *             @OA\Property(property="name", type="string", example="Admin"),
     *             @OA\Property(property="email", type="string", example="admin@gmail.com"),
     *             @OA\Property(property="email_verified_at", type="string", format="date-time", example="2023-04-06T13:58:12.000000Z"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-04-06T13:58:12.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-04-06T13:58:12.000000Z")
     *         ),
     *         @OA\Property(
     *             property="recipes",
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=26),
     *                 @OA\Property(property="user_id", type="integer", example=2),
     *                 @OA\Property(property="title", type="string", example="sdgd"),
     *                 @OA\Property(property="description", type="string", example=""),
     *                 @OA\Property(property="ingredients", type="string", example=""),
     *                 @OA\Property(property="instructions", type="string", example=""),
     *                 @OA\Property(property="image", type="string", example=""),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-04-07T11:26:25.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-04-07T11:26:25.000000Z")
     *             )
     *         )
     *     )
     * ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\Models\User] 1")
     *         )
     *     )
     * )
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        $recipes = $user->recipes()->orderByDesc('created_at')->get();
        $user = auth()->user() && $user->id === auth()->user()->id ? $user : ['name' => $user->name];

        return response()->json([
            'user' => $user,
            'recipes' => $recipes,
        ]);
    }
}
