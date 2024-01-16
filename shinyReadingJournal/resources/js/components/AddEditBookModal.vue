<template>
    <v-dialog v-model="dialog" max-width="1200" max-height="920">
        <v-card>
            <v-card-title>
                <h5 class="modal-title">{{ isEditMode === 'edit' ? 'Edit Book' : 'Add Book' }}</h5>
                <v-btn icon @click="closeModal">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
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
                <v-container>
                    <h2  class="mb-5">Didn't find what you were looking for? Add it yourself!</h2>
                    <v-form @submit.prevent="saveBook" enctype="multipart/form-data">
                        <input type="hidden" id="edit-id">
                        <v-text-field label="Title" v-model="editedBook.title" required></v-text-field>
                        <v-text-field label="Author" v-model="editedBook.author" required></v-text-field>
                        <v-text-field label="Series" v-model="editedBook.series"></v-text-field>
                        <v-file-input label="Cover Image" v-model="selectedCoverFile" @change="handleFileChange" accept="image/*"></v-file-input>
                        <input type="text" v-if="selectedCoverFile" :value="selectedCoverFile.name" readonly>
                        <v-btn type="submit" color="primary">{{ isEditMode ? 'Update' : 'Add' }}</v-btn>
                    </v-form>
                </v-container>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>
<script>
import axios from 'axios';

export default {
    data() {
        return {
            editedBook: {
                title: '',
                author: '',
                series: '',
                cover: null,
            },
            searchQuery: '',
            books: [],
            selectedCoverFile: null, // Store the selected file
            isEditMode: false,
            bookId: null,
            dialog: false, // Define 'dialog' here
        };
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
        },
        openModal(book, mode) {
            console.log("openModal called with mode:", mode);
            console.log("Book data received in openModal:", book);

            this.selectedCoverFile = null;

            this.editedBook = { ...book };
            this.isEditMode = mode === 'edit';
            if (this.isEditMode) {
                this.bookId = book.id;
            }
            this.dialog = true;
        },
        async saveBook() {
            console.log("Saving book with data:", this.editedBook);
            try {
                const formData = new FormData();
                formData.append('title', this.editedBook.title);
                formData.append('author', this.editedBook.author);
                formData.append('series', this.editedBook.series);

                // Check if 'cover' is not null and add it to formData if it exists
                if (this.editedBook.newCover) {
                    formData.append('cover', this.editedBook.newCover);
                }

                console.log('FormData before sending:', Array.from(formData.entries()));

                let response;
                if (this.isEditMode) {
                    // Using fetch with POST method and _method field for PUT spoofing
                    response = await fetch(`/api/books/${this.bookId}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                } else {
                    // Using fetch for a standard POST request
                    response = await fetch('/api/books', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                }

                const responseData = await response.json();
               // console.log('Response after sending:', responseData);

                // Handle success
                if (response.ok) {
                  //  console.log('Emitting book-saved with:', { book: responseData, isNew: !this.isEditMode });
                    this.$emit('book-saved', { book: responseData, isNew: !this.isEditMode });

                    this.closeModal();

                } else {
                    console.error('Failed to save the book:', responseData);
                }
            } catch (error) {
                // Handle error
                console.error('Error saving book:', error);
            }
        },
        handleFileChange(event) {
            this.editedBook.newCover = event.target.files[0];
        },
        closeModal() {
            this.isEditMode = false;
            this.dialog = false; // Close the modal by setting 'dialog' to false
        },
    },
};
</script>
