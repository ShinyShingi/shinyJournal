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
export default {
    name: 'Navbar',
    computed: {
        isLoggedIn() {
            return !!localStorage.getItem('token');
        }
    },
    methods: {
        addNewBook() {
            // Handle the Add New Book button click event
            console.log("Add New Book clicked");
        },
        logIn() {
            // Emit an event that App.vue can listen for to open the login modal
            this.$emit('login-click');
        },
        logout() {
            localStorage.removeItem('token');
            this.$router.push({ path: '/' });        },
        register() {
            this.$emit('register-click');
        }
    }
};
</script>
