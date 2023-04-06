<template>
    <div class="d-flex align-self-center">
        <v-select
            v-model="filters.searchType"
            :items="filters.searchTypes"
            class="mr-1"
            hide-details
            label="Search Type"
        />
        <v-text-field
            v-model="filters.search"
            class="mr-1"
            clearable
            hide-details
            label="Search"
        />
        <v-select
            v-model="filters.category_ids"
            :items="filters.categories"
            chips
            class="mr-1"
            clearable
            hide-details
            item-title="name"
            item-value="id"
            label="Category"
            multiple
        />
        <v-btn color="primary" height="auto" @click="searchRecipes">Search</v-btn>
    </div>
</template>

<script>
export default {
    name: 'SearchPanel',

    props: {
        filters: {
            type: Object,
            required: true,
        },
    },

    methods: {
        searchRecipes() {
            this.$emit('search-recipes');
        },
    },
};
</script>
