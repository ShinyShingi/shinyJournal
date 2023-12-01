<template>
    <v-dialog v-model="dialog" persistent max-width="400">
        <v-card>
            <v-card-title class="d-flex justify-space-between">
                <h3 class="headline my-auto">Login</h3>
                <v-btn icon text class="no-background-effect" @click="closeDialog">
                    <v-icon>mdi-close</v-icon>
                </v-btn>

            </v-card-title>
            <v-card-text>
                <v-container>
                    <v-form>
                        <v-text-field label="Email" v-model="email" type="email"></v-text-field>
                        <v-text-field label="Password" v-model="password" type="password"></v-text-field>
                    </v-form>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="submitForm">Login</v-btn>
                <v-btn color="red" text @click="signInWithGoogle">Sign in with Google</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import axios from "axios";
import router from '../router.js'

export default {
    props: ['showDialog'],
    watch: {
        showDialog(newValue) {
            this.dialog = newValue;
        }
    },
    data() {
        return {
            dialog: false,
            email: '',
            password: ''
        };
    },
    // mounted() {
    //     this.loadGoogleApiScript();
    // },
    methods: {
        async submitForm() {
            try {
                const response = await axios.post('/login', {
                    email: this.email,
                    password: this.password
                });
                localStorage.setItem('token', response.data.token);
                this.$emit('login-successful'); // Emit the event here
                this.$router.push({ name: 'UserProfile' });
            } catch (error) {
                console.error(error);
            }

            this.closeDialog();
        },
        closeDialog() {
            this.dialog = false;
            this.$emit('update:showDialog', false); // Notify parent to update the prop
            this.$emit('close-modal');
        },
        // loadGoogleApiScript() {
        //     if (document.querySelector('script[src="https://apis.google.com/js/platform.js"]')) {
        //         return;
        //     }
        //
        //     const script = document.createElement('script');
        //     script.src = 'https://apis.google.com/js/platform.js';
        //     script.async = true;
        //     script.defer = true;
        //     document.head.appendChild(script);
        //
        //     script.onload = () => this.initializeGoogleAPI();
        // },
        // initializeGoogleAPI() {
        //     gapi.load('auth2', () => {
        //         gapi.auth2.init({
        //             client_id: '800650533712-on2c09fdi5k91vgj5lglkofnsrqek6eb.apps.googleusercontent.com',
        //             // Additional configuration options
        //         });
        //     });
        // },
        //
        // signInWithGoogle() {
        //     const auth2 = gapi.auth2.getAuthInstance();
        //     auth2.signIn().then(googleUser => {
        //         this.$router.push('/profile');
        //     });
        // }

    }
};
</script>
<style>
.no-background-effect {
    background-color: transparent !important;
    box-shadow: none !important;
}
.no-background-effect:hover {
    background-color: transparent !important;

    box-shadow: none !important;
}
</style>
