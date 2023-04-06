<template>
    <v-chip v-for="route in routes">
        <router-link :to="route.to">{{ route.name }}</router-link>
    </v-chip>
</template>

<script setup>
const routes = [
    {name: 'Recipes', to: {name: 'recipes.index'}},
];

</script>
