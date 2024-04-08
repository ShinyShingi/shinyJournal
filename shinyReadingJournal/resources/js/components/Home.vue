<template>
    <v-container class="home">
        <h2>Most Popular Books</h2>
        <v-row>
            <BookComponent
                v-for="book in popularBooks"
                :key="book.id"
                :book="book"
                @edit-book="editBook"
                @remove-book="removeBook"
                @updateStatus="updateStatus"
            />
        </v-row>

        <h2>Highest Rated Books</h2>
        <v-row>
            <BookComponent
                v-for="book in highestRatedBooks"
                :key="book.id"
                :book="book"
                @edit-book="editBook"
                @remove-book="removeBook"
                @updateStatus="updateStatus"
            />
        </v-row>
    </v-container>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';
import BookComponent from './Book.vue'; // Ensure this matches how you use it in the template

const popularBooks = ref([]);
const highestRatedBooks = ref([]);

onMounted(async () => {
    try {
        const [popularResponse, ratedResponse] = await Promise.all([
            axios.get('/api/books/popular'),
            axios.get('/api/books/highest-rated')
        ]);
        popularBooks.value = popularResponse.data;
        highestRatedBooks.value = ratedResponse.data;
    } catch (error) {
        console.error('Error fetching books:', error);
    }
});
</script>


<style>
.home {
    margin-top: 5rem;
}
h2 {
    margin-top: 1rem;
}
.details-container {
    min-height: 50px !important;
}
</style>
