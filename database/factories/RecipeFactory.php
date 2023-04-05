<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'ingredients' => $this->faker->paragraphs(3, true),
            'instructions' => $this->faker->paragraphs(5, true),
            'image' => $this->faker->imageUrl(),
        ];
    }

    public function configure(): RecipeFactory
    {
        return $this->afterCreating(function (Recipe $recipe) {
            $categories = Category::inRandomOrder()->take(random_int(1, 3))->pluck('id');
            $recipe->categories()->attach($categories);
        });
    }
}
