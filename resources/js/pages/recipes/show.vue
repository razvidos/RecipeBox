<template>
    <v-container>
        <v-card>
            <v-card-title>
                <v-btn icon="mdi-view-list" to="/recipes" variant="plain"/>
                {{ recipe.title }}
                <v-icon v-if="isOwner">mdi-account-check</v-icon>
            </v-card-title>

            <v-card-subtitle>
                Categories:
                <span v-for="(category, index) in recipe.categories" :key="index">
                    {{ category.name }}{{ index < recipe.categories.length - 1 ? ', ' : '' }}
                </span>
            </v-card-subtitle>

            <v-card-text>
                <v-img
                    v-if="recipe.image"
                    :lazy-src="recipe.image"
                    :src="recipe.image"
                    contain
                    height="auto"
                    width="100%"
                ></v-img>
                <div v-html="recipe.description"></div>
            </v-card-text>

            <v-card-text>
                <h4>Ingredients:</h4>
                <div v-html="recipe.ingredients"></div>
            </v-card-text>

            <v-card-text>
                <h4>Instructions:</h4>
                <div v-html="recipe.instructions"></div>
            </v-card-text>
            <v-divider></v-divider>

            <v-card-actions>
                <v-spacer/>
                <v-btn
                    v-if="isOwner"
                    :to="{name: 'recipes.edit', params: {id: $route.params.id}}"
                    class="mr-4"
                    color="danger"
                    v-text="'Edit Recipe'"/>
                <v-btn
                    v-if="recipe.user_id"
                    :to="{name: 'users.show', params: {id: recipe.user_id}}"
                    v-text="'View Author\'s Profile'"/>
            </v-card-actions>
        </v-card>
    </v-container>
</template>

<script>
import axios from 'axios';

export default {
    name: 'RecipeShow',
    props: ['id'],
    data() {
        return {
            recipe: {},
            isOwner: false,
        }
    },
    created() {
        this.getRecipe();
    },
    methods: {
        getRecipe() {
            axios.get(`/api/recipes/${this.id}`)
                .then(response => {
                    this.recipe = response.data;
                    this.isOwner = this.recipe.user_id === this.$authStore.user_id;

                })
                .catch(error => {
                    console.log(error);
                });
        },
    }
}
</script>
