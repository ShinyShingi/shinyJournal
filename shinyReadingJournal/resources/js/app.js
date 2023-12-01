import { createApp } from 'vue'
import App from './App.vue'
import '@mdi/font/css/materialdesignicons.css'
// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import router from './router.js';
import { reactive, provide, inject } from "vue";

import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const vuetify = createVuetify({
    components,
    directives,
})

const app = createApp(App);

// Check if the user is authenticated in local storage
const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';

// Create a reactive authState object with the initial value from local storage
const authState = reactive({
    isLoggedIn: isLoggedIn
});

// Provide the authState to the app
app.provide('authState', authState);

app.use(vuetify);
app.use(router);

app.mount('#app')
