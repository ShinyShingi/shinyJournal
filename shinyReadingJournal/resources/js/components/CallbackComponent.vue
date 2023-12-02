<template>
    <div>
        <p>Processing authentication...</p>
    </div>
</template>

<script>
export default {
    async mounted() {
        const idToken = this.extractIdTokenFromUrl();

        // Send the ID token to your server for verification and authentication.
        // You should implement this part on your server.

        // After authentication is complete, you can redirect the user to the desired route.
        this.$router.push('/profile'); // Redirect to the user's profile page.
    },
    methods: {
        extractIdTokenFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('id_token');
        },
    },
};
</script>
