<script>

//import Books from "./components/Books.vue";
import Navbar from './components/Navbar.vue';
import Login from "./components/Login.vue";
import Register from "./components/Register.vue";
import UserProfile from "./components/UserProfile.vue";
import { reactive, watch, provide } from 'vue';


export default {

    components: {
        Register,
        Navbar,
        Login,
        UserProfile
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
    }
}

</script>


<template>
    <v-app>
        <navbar :auth-state="authState"
                @logout-successful="handleLogout"
                @login-click="toggleLoginModal"
                @register-click="toggleRegisterModal"></navbar>
        <login @login-successful="handleLogin"
               :show-dialog="isLoginModalVisible"
               @close-modal="isLoginModalVisible = false"></login>
        <register :show-dialog="isRegisterModalVisible"
                  @close-modal="isRegisterModalVisible = false"></register>

        <router-view></router-view>
    </v-app>
</template>
<style>

</style>


