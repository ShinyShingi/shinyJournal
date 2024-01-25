<script>

//import Books from "./components/Books.vue";
import Navbar from './components/Navbar.vue';
import Login from "./components/Login.vue";
import Register from "./components/Register.vue";
import UserProfile from "./components/UserProfile.vue";
import AddEditBookModal from "./components/AddEditBookModal.vue";
import { reactive, watch, provide } from 'vue';
import vue3StarRatings from "vue3-star-ratings";


export default {

    components: {
        Register,
        Navbar,
        Login,
        UserProfile,
        AddEditBookModal,
        vue3StarRatings
        //Books
    },
    setup() {
        const authState = reactive({
            isLoggedIn: !!localStorage.getItem('token')
        });
        provide('authState', authState);

        watch(() => authState.isLoggedIn, (newVal) => {
            console.log('authState.isLoggedIn changed:', newVal);
        });

        return { authState };

    },
    data() {
        return {
            isLoginModalVisible: false,
            isRegisterModalVisible: false
        };
    },
    methods: {
        toggleLoginModal() {
            this.isLoginModalVisible = !this.isLoginModalVisible;

        },
        toggleRegisterModal() {
            this.isRegisterModalVisible = !this.isRegisterModalVisible;
        },
        handleLogin() {
            this.authState.isLoggedIn = true;
            console.log("authState.isLoggedIn updated to true:", this.authState.isLoggedIn);
        },
        handleLogout() {
            this.authState.isLoggedIn = false;
        },
        openAddEditBookModal() {
            // Call the openModal method in your component to open the modal for adding a new book
            this.$refs.addEditBookModal.openModal({}, 'add');
        },
    }
}

</script>


<template>
    <v-app>
        <navbar :auth-state="authState"
                @logout-successful="handleLogout"
                @login-click="toggleLoginModal"
                @register-click="toggleRegisterModal"
                @add-book-click="openAddEditBookModal"></navbar>
        <login @login-successful="handleLogin"
               :show-dialog="isLoginModalVisible"
               @close-modal="isLoginModalVisible = false"></login>
        <register :show-dialog="isRegisterModalVisible"
                  @close-modal="isRegisterModalVisible = false"></register>
        <add-edit-book-modal ref="addEditBookModal"></add-edit-book-modal>
        <router-view></router-view>
    </v-app>
</template>
<style>

</style>


