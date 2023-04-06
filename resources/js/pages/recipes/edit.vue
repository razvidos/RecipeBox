<template>
    <v-container>

        <v-card>
            <v-card-title>
                <v-btn icon="mdi-view-list" variant="plain" @click="recipeIndex"/>
                Edit Recipe
                <v-btn icon="mdi-eye" variant="plain" @click="showRecipe"/>
            </v-card-title>
            <v-card-text>

                <v-form>
                    <v-text-field v-model="form.title" label="Title"/>
                    <v-textarea v-model="form.description" label="Description"/>
                    <v-textarea v-model="form.ingredients" label="Ingredients"/>
                    <v-textarea v-model="form.instructions" label="Instructions"/>
                    <v-file-input label="Image" @change="defineImage"/>
                    <v-row class="mb-2">
                        <v-spacer/>
                        <v-img v-if="imageUrl" :src="imageUrl" alt="preview" contain height="200" width="200"/>
                    </v-row>
                    <v-select
                        v-model="form.category_ids"
                        :items="categories"
                        chips
                        item-title="name"
                        item-value="id"
                        label="Categories"
                        multiple
                    />
                </v-form>
                <v-alert v-if="errors && Object.keys(errors).length > 0" type="error">
                    <ul>
                        <li v-for="(error, key) in errors" :key="key">{{ error[0] }}</li>
                    </ul>
                </v-alert>
            </v-card-text>
            <v-card-actions>
                <v-spacer/>

                <v-btn
                    class="mr-4"
                    color="success"
                    @click="updateRecipe"
                    v-text="'Update Recipe'"/>
                <v-btn
                    color="error"
                    @click="deleteRecipe"
                    v-text="'Delete Recipe'"/>
            </v-card-actions>
        </v-card>
    </v-container>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            form: {
                title: "",
                description: "",
                ingredients: "",
                instructions: "",
                image: null,
                category_ids: []
            },
            categories: [],
            imageUrl: null,
            errors: {},
        };
    },
    created() {
        axios.get("/api/categoryList").then(response => {
            this.categories = response.data;
        });
        axios.get(`/api/recipes/${this.$route.params.id}`).then(response => {
            const recipe = response.data;
            this.form.title = recipe.title;
            this.form.description = recipe.description;
            this.form.ingredients = recipe.ingredients;
            this.form.instructions = recipe.instructions;
            this.form.category_ids = recipe.categories.map(category => category.id);
        });
    },
    methods: {
        defineImage(e) {
            this.form.image = e.target.files[0]
            this.imageUrl = URL.createObjectURL(this.form.image)
        },
        updateRecipe() {
            const formData = new FormData();
            formData.append('_method', 'put');
            formData.append("title", this.form.title);
            formData.append("description", this.form.description);
            formData.append("ingredients", this.form.ingredients);
            formData.append("instructions", this.form.instructions);
            if (this.form.image) {
                formData.append("image", this.form.image);
            }
            formData.append("category_ids[]", this.form.category_ids);

            axios.post(
                `/api/recipes/${this.$route.params.id}`, formData, {
                    headers: {'Content-Type': 'multipart/form-data; enctype="multipart/form-data"'}
                }).then(() => {
                this.$router.push(`/recipes/${this.$route.params.id}`);
                this.errors = {};

            }).catch(error => {
                this.errors = error.response.data.errors;
            });
        },
        recipeIndex() {
            if (confirm('Are you sure you want to left this page?')) {
                this.$router.push({name: 'recipes.index'});
            }
        },
        showRecipe() {
            const id = this.$route.params.id;
            if (confirm('Are you sure you want to left this page?')) {
                this.$router.push({name: 'recipes.show', params: {id}});
            }
        },
        deleteRecipe() {
            const id = this.$route.params.id;
            if (confirm('Are you sure you want to delete this recipe?')) {
                window.axios.delete(`/api/recipes/${id}`)
                    .then(response => {
                        this.$router.push({name: 'recipes'});
                    })
                    .catch(error => {
                        console.log(error);
                    });
            }
        },

    }
};
</script>
