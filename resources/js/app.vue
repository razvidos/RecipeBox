<template>
    <v-app>
        <v-app-bar app color="primary" dark>
            <v-toolbar-title>{{ appName }}</v-toolbar-title>
            <v-spacer></v-spacer>

            <v-btn :to="{name: 'recipes.index'}">Recipes</v-btn>
            <v-btn v-if="this.$authStore.isLoggedIn" :to="{name: 'users.show', params: {id: this.$authStore.user.id}}"
                   class="mr-6">
                Profile
            </v-btn>
            <v-btn v-if="!this.$authStore.isLoggedIn" to="/login">Login</v-btn>
            <v-btn v-if="!this.$authStore.isLoggedIn" to="/register">Register</v-btn>
            <v-btn v-if="this.$authStore.isLoggedIn" variant="outlined" @click="logout">Logout</v-btn>
        </v-app-bar>

        <v-main>
            <router-view></router-view>
        </v-main>
    </v-app>
</template>

<script>
import navBar from './components/global/navBar.vue'

export default {
    components: {navBar,},
    data() {
        return {
            appName: 'RecipeBox',
        };
    },
    mounted() {
        this.$authStore.isLoggedIn = window.auth_user !== null
        this.$authStore.user = window.auth_user
    },
    methods: {
        async logout() {
            await this.$authStore.logout();
            this.$router.push('/');

        }
    },
};
</script>
