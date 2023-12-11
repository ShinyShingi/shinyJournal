<template>
    <v-app-bar app class="navbar" dark>
        <v-toolbar-title>
            <v-btn @click="goHome">
                Shiny's Reading Journal
            </v-btn>
        </v-toolbar-title>
        <v-spacer></v-spacer>
<!--        <v-btn color="green darken-3" @click="addNewBook">-->
<!--            Add New Book-->
<!--        </v-btn>-->
      <router-link v-if="isLoggedIn" class="v-btn v-theme--light text-green darken-3 v-btn--density-default v-btn--size-default v-btn--variant-text" :to="`/profile/${username}`">My Profile</router-link>
<!--      <v-btn color="green darken-3" @click="goToProfile">My Profile</v-btn>-->

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
import { ref, onMounted, inject } from 'vue';
import axios from "axios";
import AddEditBookModal from "./AddEditBookModal.vue";
import router from "../router.js";

export default {
    name: 'Navbar',
    props: {
        authState: Object
    },
    setup() {
        const authState = inject('authState');
        const username = ref('');
        onMounted(async () => {
          try {
            const response = await axios.get('/api/user');
            username.value = response.data.username; // Adjust based on your API response
          } catch (error) {
            console.error('Error fetching user:', error);
          }
        });
      return { authState, username };

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
        // addNewBook() {
        //     // Handle the Add New Book button click event
        //     this.$emit('add-book-click');
        // },
        logIn() {
            // Emit an event that App.vue can listen for to open the login modal
            this.$emit('login-click');
        },
        async logout() {

            try {
                const response = await axios.post('/logout', {});
                localStorage.removeItem('token');
                this.$emit('logout-successful'); // Emit the event here
                this.$router.push({path: '/'});
            } catch (error) {
                console.error(error);
            }

            // this.$emit('logout-click');
            //
            // this.$emit('logout-successful'); // Emit an event on successful logout
            // this.$router.push({path: '/'});


        },
        goToProfile() {
          this.$router.push(`/profile/${username.value}`);

        },
        register() {
            this.$emit('register-click');
        },
        goHome() {
            this.$router.push({path: '/'});
        }

    }
};
</script>
