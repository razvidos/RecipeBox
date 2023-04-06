<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecipeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreWhenUserIsNotLogin(): void
    {
        // Act
        $response = $this->post('/api/recipes');

        // Assert
        $response->assertForbidden();
    }

    public function testStoreWhenUserIsLogin(): void
    {
        // Arrange
        $user = User::factory()->create();
        $categoriesCount = 3;
        $categories = Category::factory()->count($categoriesCount)->create();

        $recipeData = $this->generateRecipeData($categories);

        // Act
        $response = $this->actingAs($user)->postJson('/api/recipes', $recipeData);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('recipes', $this->extractRecipeDataFrom($recipeData, $user));
        $this->assertEquals($categoriesCount, $this->countRecipeCategories($response['id'], $categories->pluck('id')->toArray()));
    }

    private function generateRecipeData(Collection $categories): array
    {
        return [
            'title' => 'Spaghetti Bolognese',
            'description' => null,
            'ingredients' => 'Spaghetti, minced beef, onion, garlic, tomato sauce',
            'instructions' => '1. Cook spaghetti. 2. Fry minced beef, onion and garlic. 3. Add tomato sauce. 4. Mix with cooked spaghetti.',
            'category_ids' => $categories->pluck('id')->toArray(),
        ];
    }

    private function extractRecipeDataFrom(array $recipeData, User $user): array
    {
        return [
            'title' => $recipeData['title'],
            'description' => '',
            'ingredients' => $recipeData['ingredients'],
            'instructions' => $recipeData['instructions'],
            'user_id' => $user->id,
        ];
    }

    private function countRecipeCategories(int $recipeId, array $categoryIds): int
    {
        return DB::table('category_recipe')
            ->where('recipe_id', $recipeId)
            ->whereIn('category_id', $categoryIds)
            ->count();
    }

    public function testStoreWhenUserIsLoginWithImage(): void
    {
        // Arrange
        $user = User::factory()->create();

        $recipeData = [
            'title' => 'Spaghetti Bolognese',
            'image' => UploadedFile::fake()->image('recipe-image.jpg', 500, 500),
        ];

        // Act
        $response = $this->actingAs($user)->postJson('/api/recipes', $recipeData);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('recipes', ['title' => $recipeData['title'],]);
        Storage::assertExists($response->json('image'));

    }

    public function testStoreWithValidationFail(): void
    {
        // Arrange
        $user = User::factory()->create();

        $recipeData = [
            'title' => str_repeat('a', 256), // greater than 255
            'category_ids' => ['f', 'g', 'h'], // non-integer value in category_ids array
        ];

        // Act
        $response = $this->actingAs($user)->postJson('/api/recipes', $recipeData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'category_ids.0', 'category_ids.1']);
    }

    public function testIndexWithoutRecipes(): void
    {
        // Act
        $response = $this->getJson('/api/recipes');

        // Assert
        $response->assertOk()->assertJsonCount(0, 'data');
    }

    public function testIndexWith1Recipe(): void
    {
        // Arrange
        User::factory()->create();
        Category::factory()->count(3)->create();
        $recipe = Recipe::factory()->create();

        // Act
        $response = $this->getJson('/api/recipes');

        // Assert
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'id' => $recipe->id,
                'title' => $recipe->title,
                'description' => $recipe->description,
                'image' => $recipe->image,
                'user_id' => $recipe->user_id,
            ]);
    }

    public function testIndexWith20Recipes(): void
    {
        // Arrange
        User::factory()->create();
        Recipe::factory(20)->create();

        // Act
        $response = $this->getJson('/api/recipes');

        // Assert
        $response->assertOk()
            ->assertJsonFragment(['total' => 20]);
    }

    public function testShow(): void
    {
        // Arrange
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->getJson('/api/recipes/' . $recipe->id);

        // Assert
        $response->assertOk();
        $response->assertJsonFragment($recipe->toArray());
    }

    public function testUpdateWhenUserIsNotOwner(): void
    {
        // Arrange
        User::factory()->create();
        $recipe = Recipe::factory()->create();
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->putJson("/api/recipes/{$recipe->id}", []);

        // Assert
        $response->assertForbidden();
    }

    public function testUpdateWhenUserIsOwnerWithoutImage(): void
    {
        // Arrange
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->putJson("/api/recipes/{$recipe->id}", [
                'title' => $recipe->title,
            ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
            'user_id' => $user->id,
            'title' => $recipe->title,
        ]);
    }

    public function testUpdateWhenUserIsOwnerWithImage(): void
    {
        // Arrange
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        $file = UploadedFile::fake()->image('test.jpg');

        // Act
        $response = $this->actingAs($user)->putJson("/api/recipes/{$recipe->id}", [
            'id' => $recipe->id,
            'title' => $recipe->title,
            'image' => $file,
        ]);

        // Assert
        $response->assertOk();

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
            'user_id' => $user->id
        ]);

        Storage::assertExists($response->json('image'));
    }

    public function testUpdateWithValidationFail(): void
    {
        // Arrange
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        $recipeData = [
            'title' => str_repeat('a', 256), // greater than 255
            'category_ids' => ['f', 0], // invalid category IDs
        ];

        // Act
        $response = $this->actingAs($user)->putJson("/api/recipes/{$recipe->id}", $recipeData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'category_ids.0', 'category_ids.1']);
    }

    public function testDestroyWhenUserIsNotOwner(): void
    {
        // Arrange
        User::factory()->create();
        $recipe = Recipe::factory()->create();
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->delete("/api/recipes/{$recipe->id}");

        // Assert
        $response->assertForbidden();
    }

    public function testDestroyWhenUserIsOwner(): void
    {
        // Arrange
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->delete("/api/recipes/{$recipe->id}");

        // Assert
        $response->assertOk()
            ->assertJson(['message' => 'Recipe deleted']);
    }
}
