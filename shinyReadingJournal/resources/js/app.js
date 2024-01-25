import { createApp } from 'vue'
import App from './App.vue'
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import router from './router.js'
import { reactive, provide, inject } from "vue"
import imagePlugin from './plugins/imagePlugin'
import vue3StarRatings from "vue3-star-ratings";

import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const app = createApp(App)

// Check if the user is authenticated in local storage
const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true'

// Create a reactive authState object with the initial value from local storage
const authState = reactive({
    isLoggedIn: isLoggedIn
})

// Provide the authState to the app
app.provide('authState', authState)

// Use Vuetify's components and directives
const vuetify = createVuetify({
    components,
    directives,
})
app.component("vue3-star-ratings", vue3StarRatings);

// Mount the app
app.use(vuetify)
app.use(router)
app.use(imagePlugin) // Use the plugin

import '../css/main.scss'

app.mount('#app')
