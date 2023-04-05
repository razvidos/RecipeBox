<template>
    <v-container fluid>
        <v-card class="mx-auto" max-width="600">
            <v-card-title class="text-h4 text-center">404 - Page not found</v-card-title>
            <v-card-text class="text-center">
                Sorry, the page you are looking for could not be found.
            </v-card-text>
            <v-card-actions class="justify-center">
                <v-btn color="primary" to="/recipes">Go to recipes</v-btn>
            </v-card-actions>
        </v-card>
    </v-container>
</template>

<script>
export default {
    name: "NotFound",
};
</script>

<style scoped>
.v-container {
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
