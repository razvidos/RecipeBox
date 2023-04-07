<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testShowReturnsCorrectData(): void
    {
        // Arrange
        $user = User::factory()->create();
        $recipe1 = Recipe::factory()->create();
        $recipe2 = Recipe::factory()->create();

        // Act
        $response = $this->actingAs($user)->getJson(route('users.show', $user));

        // Assert
        $response->assertOk()
            ->assertJson([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at->toISOString(),
                    'created_at' => $user->created_at->toISOString(),
                    'updated_at' => $user->updated_at->toISOString(),
                ],
                'recipes' => [
                    [
                        'id' => $recipe1->id,
                        'title' => $recipe1->title,
                        'description' => $recipe1->description,
                        'ingredients' => $recipe1->ingredients,
                        'instructions' => $recipe1->instructions,
                        'image' => $recipe1->image,
                        'created_at' => $recipe1->created_at->toISOString(),
                        'updated_at' => $recipe1->updated_at->toISOString(),
                        'user_id' => $user->id,
                    ],
                    [
                        'id' => $recipe2->id,
                        'title' => $recipe2->title,
                        'description' => $recipe2->description,
                        'ingredients' => $recipe2->ingredients,
                        'instructions' => $recipe2->instructions,
                        'image' => $recipe2->image,
                        'created_at' => $recipe2->created_at->toISOString(),
                        'updated_at' => $recipe2->updated_at->toISOString(),
                        'user_id' => $user->id,
                    ],
                ],
            ]);
    }

    public function testShowReturnsCorrectDataForAnotherUser(): void
    {
        // Arrange
        $user = User::factory()->create();

        $recipe1 = Recipe::factory()->create();
        $recipe2 = Recipe::factory()->create();
        $recipe3 = Recipe::factory()->create();

        $otherUser = User::factory()->create();

        // Act
        $response = $this->actingAs($otherUser)->getJson(route('users.show', $user));

        // Assert
        $response->assertOk()
            ->assertJson([
                'user' => [
                    'name' => $user->name,
                ],
                'recipes' => [
                    [
                        'id' => $recipe1->id,
                        'title' => $recipe1->title,
                        'description' => $recipe1->description,
                        'image' => $recipe1->image,
                        'created_at' => $recipe1->created_at->toISOString(),
                        'updated_at' => $recipe1->updated_at->toISOString(),
                    ],
                    [
                        'id' => $recipe2->id,
                        'title' => $recipe2->title,
                        'description' => $recipe2->description,
                        'image' => $recipe2->image,
                        'created_at' => $recipe2->created_at->toISOString(),
                        'updated_at' => $recipe2->updated_at->toISOString(),
                    ],
                    [
                        'id' => $recipe3->id,
                        'title' => $recipe3->title,
                        'description' => $recipe3->description,
                        'image' => $recipe3->image,
                        'created_at' => $recipe3->created_at->toISOString(),
                        'updated_at' => $recipe3->updated_at->toISOString(),
                    ],
                ],
            ])
            ->assertJsonMissingExact(['email' => $user->email]);
    }
}
