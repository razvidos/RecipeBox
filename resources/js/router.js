import {createRouter, createWebHashHistory} from "vue-router";
import Login from "./pages/auth/login.vue"
import Register from "./pages/auth/register.vue"
import NotFound from "./pages/NotFound.vue"

import RecipeIndex from "./pages/recipes/index.vue"
import RecipeShow from "./pages/recipes/show.vue"
import RecipeCreate from "./pages/recipes/create.vue"
import RecipeEdit from "./pages/recipes/edit.vue"

import UserShow from "./pages/users/show.vue"

export default createRouter({
    history: createWebHashHistory(),
    routes: [
        {
            path: '/login',
            name: 'login',
            component: Login
        },
        {
            path: '/register',
            name: 'register',
            component: Register
        },


        {
            path: '/recipes',
            children: [
                {
                    path: '/recipes',
                    name: 'recipes.index',
                    component: RecipeIndex,
                },
                {
                    path: '/recipes/create',
                    name: 'recipes.create',
                    component: RecipeCreate,
                    meta: {requiresAuth: true}
                },
                {
                    path: '/recipes/:id',
                    name: 'recipes.show',
                    component: RecipeShow,
                    props: true
                },
                {
                    path: '/recipes/:id/edit',
                    name: 'recipes.edit',
                    component: RecipeEdit,
                    props: true,
                    meta: {requiresAuth: true}
                },
            ]
        },
        {
            path: '/users',
            children: [
                {
                    path: '/users/:id',
                    name: 'users.show',
                    component: UserShow,
                    props: true
                }
            ],
        },
        {
            path: '/:pathMatch(.*)',
            name: 'NotFound',
            component: NotFound
        },
    ]
});
