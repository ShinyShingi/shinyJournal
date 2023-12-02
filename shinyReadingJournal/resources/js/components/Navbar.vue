<template>
    <v-app-bar app class="navbar" dark>
        <v-toolbar-title>Shiny's Reading Journal</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn color="green darken-3" @click="addNewBook">
            Add New Book
        </v-btn>
        <v-btn color="green darken-3" v-if="isLoggedIn" @click="logout">
            Logout
        </v-btn>
        <v-btn  color="green darken-3" v-else @click="logIn">
            Login
        </v-btn>
        <v-btn color="green darken-3" v-if="!isLoggedIn" @click="register">
           Register
        </v-btn>
    </v-app-bar>
</template>

<script>
import { inject } from 'vue';
import AddEditBookModal from "./AddEditBookModal.vue";
import { ref } from 'vue';

export default {
    name: 'Navbar',
    props: {
        authState: Object
    },
    setup() {
        const authState = inject('authState');
        return { authState };
    },
    computed: {
        isLoggedIn() {
            return this.authState.isLoggedIn;
        },
        isLoggedIn2() {

            return !!localStorage.getItem('token');

        }
    },
    methods: {
        addNewBook() {
            // Handle the Add New Book button click event
            console.log("Add New Book clicked");
            this.$emit('add-book-click');
        },
        logIn() {
            // Emit an event that App.vue can listen for to open the login modal
            this.$emit('login-click');
        },
        logout() {
            localStorage.removeItem('token');
            this.$emit('logout-successful'); // Emit an event on successful logout
            this.$router.push({path: '/'});
        },
        register() {
            this.$emit('register-click');
        },

    }
};
</script>
