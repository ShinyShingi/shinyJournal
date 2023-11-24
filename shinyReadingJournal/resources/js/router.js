import { createRouter, createWebHistory } from 'vue-router';
import UserProfile from './components/UserProfile.vue';

const routes = [
    { path: '/profile', component: UserProfile, meta: { requiresAuth: true }, name: 'UserProfile' },

    // ... other routes ...
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = !!localStorage.getItem('token');

    // Assuming UserProfile is the protected route
    if (to.name === 'UserProfile' && !isAuthenticated) {
        // If the user is not authenticated and trying to access the UserProfile, redirect to the main page
        next({path: '/'});
    } else {
        // Proceed to the route
        next();
    }
});

export default router;
