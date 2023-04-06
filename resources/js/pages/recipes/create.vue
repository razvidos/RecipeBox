<template>
    <v-container>
        <v-card>
            <v-card-title>
                <v-btn icon="mdi-view-list" variant="plain" @click="recipeIndex"/>
                Create Recipe
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
                <v-btn class="mr-4" color="primary" @click="createRecipe" v-text="'Create Recipe'"/>
                <v-btn color="error" @click="recipeIndex" v-text="'Cancel'"/>
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
                category_ids: [],
            },
            categories: [],
            imageUrl: null,
            errors: {},
        };
    },
    created() {
        axios.get("/api/categoryList").then((response) => {
            this.categories = response.data;
        });
    },
    methods: {
        defineImage(e) {
            this.form.image = e.target.files[0];
            this.imageUrl = URL.createObjectURL(this.form.image);
        },
        createRecipe() {
            const formData = new FormData();
            formData.append("title", this.form.title);
            formData.append("description", this.form.description);
            formData.append("ingredients", this.form.ingredients);
            formData.append("instructions", this.form.instructions);
            if (this.form.image) {
                formData.append("image", this.form.image);
            }
            [...this.form.category_ids].forEach((categoryId) => {
                formData.append("category_ids[]", categoryId);
            });

            axios
                .post(`/api/recipes`, formData, {
                    headers: {"Content-Type": "multipart/form-data; enctype='multipart/form-data'"},
                })
                .then((response) => {
                    this.$router.push(`/recipes/${response.data.id}`);
                    this.errors = {};
                })
                .catch((error) => {
                    this.errors = error.response.data.errors;
                });
        },
        recipeIndex() {
            if (confirm("Are you sure you want to left this page?")) {
                this.$router.push({name: "recipes.index"});
            }
        },
    },
};
</script>
