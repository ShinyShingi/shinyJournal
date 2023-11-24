import { createApp } from 'vue'
import App from './App.vue'
import '@mdi/font/css/materialdesignicons.css'
// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import router from './router.js';

import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const vuetify = createVuetify({
    components,
    directives,
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    if (token) {
        // User is considered authenticated
        if (to.name === 'Login') {
            next({ name: 'UserProfile' }); // Redirect to profile if trying to access login page
        } else {
            next(); // Proceed to the route
        }
    } else {
        if (to.matched.some(record => record.meta.requiresAuth)) {
            next({ name: 'Login' }); // Redirect to login if the route requires auth
        } else {
            next(); // Proceed to the route
        }
    }
});

createApp(App).use(vuetify).use(router).mount('#app')
