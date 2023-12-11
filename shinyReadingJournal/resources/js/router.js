import { createRouter, createWebHistory } from 'vue-router';
import UserProfile from './components/UserProfile.vue';
import Home from "./components/Home.vue";
import CallbackComponent from "./components/CallbackComponent.vue";

const isAuthenticated = () => !!localStorage.getItem('token');

const routes = [
    { path: '/', component: Home, name: 'Home' },
    {
        path: '/profile/:username',
        component: UserProfile,
        name: 'UserProfile',
        meta: { requiresAuth: true },
    },
    { path: '/callback', name: 'Callback', component: CallbackComponent },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !isAuthenticated()) {
        next('/'); // Redirect to home if not authenticated
    } else {
        next(); // Proceed to the intended route
    }
});

export default router;
