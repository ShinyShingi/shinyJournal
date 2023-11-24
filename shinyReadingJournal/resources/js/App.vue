<script>

//import Books from "./components/Books.vue";
import Navbar from './components/Navbar.vue';
import Login from "./components/Login.vue";
import Register from "./components/Register.vue";

export default {
    components: {
        Register,
        Navbar,
        Login,
        //Books
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
        }
    }
}

</script>


<template>
    <v-app>
        <navbar @login-click="toggleLoginModal" @register-click="toggleRegisterModal"></navbar>
        <login :show-dialog="isLoginModalVisible" @close-modal="isLoginModalVisible = false"></login>
        <register :show-dialog="isRegisterModalVisible" @close-modal="isRegisterModalVisible = false"></register>
    </v-app>
</template>
<style>

</style>


