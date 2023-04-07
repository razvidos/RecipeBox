<template>
    <v-container fluid>
        <search-panel :filters="filters" @search-recipes="searchRecipes"/>

        <v-card>
            <v-card-title class="text-h4">
                Recipes
                <v-btn
                    v-if="this.$authStore.isLoggedIn"
                    color="primary" icon="mdi-plus"
                    @click="$router.push({ name: 'recipes.create' })"/>

            </v-card-title>
            <v-card-text>
                <v-list>
                    <v-list-item v-for="recipe in recipes" :key="recipe.id">
                        <v-row>
                            <v-col cols="auto">
                                <v-btn
                                    :to="{ name: 'recipes.show', params: { id: recipe.id } }"
                                    icon="mdi-food"
                                    variant="text"
                                />
                            </v-col>
                            <v-col>
                                <v-list-item-title>
                                    {{ recipe.title }}
                                    <v-icon v-if="isOwner(recipe.user_id)">mdi-account-check</v-icon>
                                </v-list-item-title>
                                <v-list-item-subtitle>{{ recipe.description }}</v-list-item-subtitle>
                                <v-list-item-subtitle>
                                    Categories:
                                    <span v-for="(category, index) in recipe.categories" :key="index">
                                {{ category.name }}{{ index < recipe.categories.length - 1 ? ', ' : '' }}
                            </span>
                                </v-list-item-subtitle>
                            </v-col>
                        </v-row>

                    </v-list-item>
                </v-list>
            </v-card-text>
            <v-card-actions>
                <v-row justify="center">
                    <v-pagination
                        v-model="page"
                        :length="totalPages"
                        :total-visible="5"
                        @update:modelValue="searchRecipes"/>
                </v-row>
            </v-card-actions>
        </v-card>
    </v-container>
</template>

<script>
import SearchPanel from './../../components/recipes/SearchPanel.vue';

export default {
    name: 'RecipeList',
    components: {SearchPanel},
    data() {
        return {
            recipes: [],
            filters: {
                search: '',
                searchType: 'simple',
                searchTypes: [],
                category_ids: null,
                categories: [],
            },

            page: 1,
            perPage: 10,
            totalRecipes: 0,

        };
    },

    created() {
        // fetch categories to populate the category select dropdown
        window.axios.get('/api/categoryList').then(response => {
            this.filters.categories = response.data;
        });

        window.axios.get('/api/recipes/searchTypes').then(response => {
            this.filters.searchTypes = response.data;
        });


        // search for recipes on component creation
        this.searchRecipes();
    },

    methods: {
        searchRecipes() {
            this.recipes = [];
            let params = {
                page: this.page,
                per_page: this.perPage,
                searchType: this.filters.searchType,
            };

            if (this.filters.search) {
                params.keyword = this.filters.search;
            }

            if (this.filters.category_ids) {
                params.category_ids = this.filters.category_ids;
            }

            axios.get('/api/recipes', {params}).then(response => {
                this.recipes = response.data.data;
                this.totalRecipes = response.data.total;
            });
        },
        isOwner(user_id) {
            return user_id === this.$authStore.user_id;
        }
    },

    computed: {
        totalPages() {
            return Math.ceil(this.totalRecipes / this.perPage);
        }
    }
};
</script>
