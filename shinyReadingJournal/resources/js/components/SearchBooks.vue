<template>
    <v-container>
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
            <v-row>
                <v-col
                    v-for="book in books"
                    :key="book.key"
                    cols="12"
                    sm="12"
                    md="4"
                    lg="3"
                    xl="2"
                    xxl="2"
                    align-self="center"
                >
                    <div v-if="books.length > 0">

                        <v-sheet class="ma-2 pa-2 book">
                            <div class="image-container">
                                <img class="img-cover" v-if="book.cover_url" :src="book.cover_url" alt="Book Cover">
                            </div>
                            <p>{{ book.title }} </p>
                            <p>{{ book.author_name ? book.author_name.join(', ') : 'Unknown Author' }}</p>
                            <p>{{book.first_publish_year}}</p>
<!--                            <p>{{book.isbn}}</p>-->
                        </v-sheet>
                    </div>
                    <div v-else>
                        <p>No books found. Try a different search!</p>
                    </div>
                </v-col>
            </v-row>
        </div>
    </v-container>
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
                const response = await axios.get('/api/search', {params: {query: this.searchQuery}});
                console.log(response.data); // Check the structure here
                // The next line assumes the structure has a 'docs' array. Adjust if necessary.
                this.books = response.data.docs || []; // Adding '|| []' as a fallback
                console.log(response.data.docs)
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
