<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <h1>{{ user.name }}'s Profile</h1>
                <span>{{ user.email }}</span>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <h2>Recipes</h2>
                <v-list>
                    <v-list-item
                        v-for="recipe in recipes"
                        :key="recipe.id"
                        :to="`/recipes/${recipe.id}`"
                    >
                        <v-list-item-title>{{ recipe.title }}</v-list-item-title>
                        <v-list-item-subtitle>Created {{ formatDate(recipe.created_at) }}</v-list-item-subtitle>
                    </v-list-item>
                </v-list>
            </v-col>
        </v-row>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            recipes: [],
            user: {},
        };
    },
    created() {
        window.axios
            .get(`/api/users/${this.$route.params.id}`)
            .then((response) => {
                this.recipes = response.data.recipes;
                this.user = response.data.user;
            })
            .catch((error) => {
                console.log(error);
            });
    },
    methods: {
        formatDate(dateString) {
            const options = {year: 'numeric', month: 'long', day: 'numeric'};
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', options);
        },
    },
};
</script>
