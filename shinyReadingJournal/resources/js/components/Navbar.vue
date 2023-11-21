<template>
    <v-app-bar app class="navbar" dark>
        <v-toolbar-title>Shiny's Reading Journal</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn color="secondary" @click="addNewBook">
            Add New Book
        </v-btn>
        <v-btn color="secondary" @click="logIn">
            Log In
        </v-btn>
        <v-btn color="secondary" @click="register">
           Register
        </v-btn>
    </v-app-bar>
</template>

<script>
export default {
    name: 'Navbar',
    methods: {
        addNewBook() {
            // Handle the Add New Book button click event
            console.log("Add New Book clicked");
            // You can implement the logic to add a new book or navigate to a new book form
        },
        logIn() {
            console.log("login is clicked")
        },
        register() {
            console.log("register is clicked")
        }
    }
};
</script>
