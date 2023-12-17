<template>
  <div class="search-container">
    <!-- Search Input Field -->
    <v-text-field
        label="Search for books"
        v-model="searchQuery"
        @keyup.enter="searchBooks"
        placeholder="Enter title, author, or ISBN"
    ></v-text-field>

    <!-- Search Button -->
    <v-btn color="primary" @click="searchBooks">
      Search
    </v-btn>

    <!-- Displaying Search Results -->
    <div v-if="books.length > 0">
      <div v-for="book in books" :key="book.key">
        <img v-if="book.cover_url" :src="book.cover_url" alt="Book Cover">

        <p>{{ book.title }} by {{ book.author_name ? book.author_name.join(', ') : 'Unknown Author' }}</p>
        <!-- Add more book details here -->
      </div>
    </div>
    <div v-else>
      <p>No books found. Try a different search!</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      searchQuery: '',
      books: []
    }
  },
  methods: {
    async searchBooks() {
      try {
        const response = await axios.get('/api/search', { params: { query: this.searchQuery } });
        console.log(response.data); // Check the structure here
        // The next line assumes the structure has a 'docs' array. Adjust if necessary.
        this.books = response.data.docs || []; // Adding '|| []' as a fallback
      } catch (error) {
        console.error(error);
      }
    }

  }
}
</script>

<style>
.search-container {
  margin-top: 5rem;
}
</style>
