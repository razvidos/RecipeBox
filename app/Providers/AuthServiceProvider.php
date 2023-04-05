<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Policies\RecipePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('store-recipe', [RecipePolicy::class, 'store']);
        Gate::define('update-recipe', [RecipePolicy::class, 'update']);
        Gate::define('delete-recipe', [RecipePolicy::class, 'delete']);
    }
}
