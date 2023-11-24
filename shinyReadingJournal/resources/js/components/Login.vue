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
    methods: {
        async submitForm() {
            try {
                const response = await axios.post('/login', {
                    email: this.email,
                    password: this.password
                });
                localStorage.setItem('token', response.data.token); // Store the token
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
