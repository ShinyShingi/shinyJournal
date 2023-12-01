import { createRouter, createWebHistory } from 'vue-router';
import UserProfile from './components/UserProfile.vue';
import Home from "./components/Home.vue";

const routes = [
    { path: '/', component: Home, name: 'Home'},
    { path: '/profile', component: UserProfile, meta: { requiresAuth: true }, name: 'UserProfile' },

];

const router = createRouter({

    mode: 'history',
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = !!localStorage.getItem('token');

    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/'); // Redirect to home if not authenticated
    } else {
        next(); // Proceed to the intended route
    }
});


export default router;
