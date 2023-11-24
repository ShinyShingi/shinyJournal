<template>
    <v-dialog v-model="dialog" persistent max-width="400">
        <v-card>
            <v-card-title class="d-flex justify-space-between">
                <h3 class="headline my-auto">Create Account</h3>
                <v-btn icon text class="no-background-effect" @click="closeDialog">
                    <v-icon>mdi-close</v-icon>
                </v-btn>

            </v-card-title>
            <v-card-text>
                <v-container>
                    <v-form>
                        <v-text-field label="Username" v-model="username" type="text"></v-text-field>
                        <v-text-field label="Email" v-model="email" type="email"></v-text-field>
                        <v-text-field label="Password" v-model="password" type="password"></v-text-field>
                        <v-text-field label="Password confirmation" v-model="passwordConfirmation" type="password"></v-text-field>
                    </v-form>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="green darken-3" text @click="submitForm">Create an Account</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import axios from 'axios';

export default {
    props: ['showDialog'],
    watch: {
        showDialog(newValue) {
            this.dialog = newValue;
        }
    },
    data() {
        return {
            dialog: false, // This will be synced with the showDialog prop
            username:'',
            email: '',
            password: '',
            passwordConfirmation: ''
        };
    },
    methods: {
        async submitForm() {
            if (this.password !== this.passwordConfirmation) {
                alert("Passwords do not match!");
                return;
            }
            try {
                const response = await axios.post('/register', {
                    username: this.username,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.passwordConfirmation
                });

                localStorage.setItem('token', response.data.token); // Store the token
                this.closeDialog();
                // You might want to redirect the user or show a success message

            } catch (error) {
                console.error("Registration error:", error);
            }
        },
        closeDialog() {
            this.dialog = false;
            this.$emit('update:showDialog', false); // Notify parent to update the prop
            this.$emit('close-modal');


        }
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
