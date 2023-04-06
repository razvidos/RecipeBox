<?php

namespace App\Models;

use Database\Factories\RecipeFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Recipe
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $description
 * @property string $ingredients
 * @property string $instructions
 * @property string $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @method static RecipeFactory factory($count = null, $state = [])
 * @method static Builder|Recipe newModelQuery()
 * @method static Builder|Recipe newQuery()
 * @method static Builder|Recipe query()
 * @method static Builder|Recipe whereCreatedAt($value)
 * @method static Builder|Recipe whereDescription($value)
 * @method static Builder|Recipe whereId($value)
 * @method static Builder|Recipe whereImage($value)
 * @method static Builder|Recipe whereIngredients($value)
 * @method static Builder|Recipe whereInstructions($value)
 * @method static Builder|Recipe whereTitle($value)
 * @method static Builder|Recipe whereUpdatedAt($value)
 * @method static Builder|Recipe whereUserId($value)
 * @mixin Eloquent
 */
class Recipe extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['title', 'description', 'ingredients', 'instructions', 'image'];

    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
}
