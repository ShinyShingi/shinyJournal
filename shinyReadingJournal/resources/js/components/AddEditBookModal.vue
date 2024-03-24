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
                    <div class="search-container" v-if="!isEditMode">
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
                        <v-row class="search-results">
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

                                    <v-sheet class="ma-2 pa-2 book"  @click="selectBookForAdd(book)">
                                        <div class="image-container">
                                            <img class="img-cover" v-if="book.cover_url || book.cover" :src="book.cover_url ? book.cover_url : book.cover" alt="Book Cover">
                                        </div>
                                        <p>{{ book.title }} </p>
                                        <p>{{ book.author_name ? book.author_name.join(', ') : book.author ? book.author : 'Unknown Author' }}</p>
                                        <p>{{book.first_publish_year}}</p>
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
                    <h2  class="mb-5" v-if="!isEditMode">Didn't find what you were looking for? Add it yourself!</h2>
                    <v-form @submit.prevent="saveBook" enctype="multipart/form-data">
                        <input type="hidden" id="edit-id">
                        <v-text-field label="Title" v-model="editedBook.title" required></v-text-field>
                        <v-text-field label="Author" v-model="editedBook.author" required></v-text-field>
                        <v-text-field label="Series" v-model="editedBook.series"></v-text-field>
                        <v-file-input label="Cover Image" v-model="selectedCoverFile" @change="handleFileChange" accept="image/*"></v-file-input>
                        <input type="text" v-if="selectedCoverFile" :value="selectedCoverFile.name" readonly>
                        <h3>Read on: </h3>
                        <v-date-picker v-model="editedBook.read_at" no-title scrollable></v-date-picker>
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
                read_at: ''
            },
            searchQuery: '',
            books: [],
            selectedCoverFile: null,
            isEditMode: false,
            bookId: null,
            dialog: false,
        };
    },
    methods: {
        async uploadImage(file) {
            console.log(file);
            const actualFile = file;
            const formData = new FormData();
            formData.append('image', actualFile);

            try {
                const response = await axios.post('/api/upload-image', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                return response.data.url;
            } catch (error) {
                console.error('Error uploading image:', error.response.data);
                return null;
            }
        },
        async searchBooks() {
            try {
                const response = await axios.get('/api/search', {params: {query: this.searchQuery}});
                console.log(response.data);
                this.books = response.data.docs || [];
                console.log(response.data.docs)
            } catch (error) {
                console.error(error);
            }
        },
        selectBookForAdd(book) {
            console.log("selected book:", book.cover_url)
            this.editedBook = {
                title: book.title,
                author: book.author_name ? book.author_name.join(', ') : '',
                series: '',
                cover: book.cover_url ? book.cover_url : null,
            };
            this.saveBook();
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

            const formData = new FormData();
            formData.append('title', this.editedBook.title);
            formData.append('author', this.editedBook.author);
            formData.append('series', this.editedBook.series || ''); // Handle null or undefined
            formData.append('read_at', this.editedBook.read_at || ''); // Handle null or undefined

            if (this.editedBook.newCover) {
                // If there's a new cover file, upload it and append the returned URL
                const coverUrl = await this.uploadImage(this.editedBook.newCover);
                if (coverUrl) {
                    // Assuming the server returns a full URL, adjust as needed
                    const relativeCoverUrl = new URL(coverUrl).pathname;
                    const adjustedCoverUrl = relativeCoverUrl.replace('/storage/app/public/', '/storage/');
                    formData.append('cover', adjustedCoverUrl);
                } else {
                    console.error('Failed to upload the cover image.');
                    return;
                }
            } else if (this.editedBook.cover) {
                // If there's an existing cover URL (e.g., from Open Library), append it directly
                // This assumes that 'cover' contains a valid URL or relative path
                formData.append('cover', this.editedBook.cover);
            }

            // Add the _method field in edit mode to indicate a PUT request
            if (this.isEditMode) {
                formData.append('_method', 'PUT');
            }

            try {
                const response = await fetch(this.isEditMode ? `/api/books/${this.editedBook.id}` : '/api/books', {
                    method: 'POST', // Use POST due to _method override
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const responseData = await response.json();
                if (response.ok) {
                    this.$emit('book-saved', { book: responseData, isNew: !this.isEditMode });
                    this.closeModal();
                } else {
                    console.error('Failed to save the book:', responseData);
                }
            } catch (error) {
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
