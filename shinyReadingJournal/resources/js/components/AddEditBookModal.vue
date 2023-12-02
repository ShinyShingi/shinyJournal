<template>
    <v-dialog v-model="dialog" max-width="400">
        <v-card>
            <v-card-title>
                <h5 class="modal-title">{{ isEditMode === 'edit' ? 'Edit Book' : 'Add Book' }}</h5>
                <v-btn icon @click="closeModal">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form @submit.prevent="saveBook" enctype="multipart/form-data">
                    <input type="hidden" id="edit-id">
                    <v-text-field label="Title" v-model="editedBook.title" required></v-text-field>
                    <v-text-field label="Author" v-model="editedBook.author" required></v-text-field>
                    <v-text-field label="Series" v-model="editedBook.series"></v-text-field>
                    <v-file-input label="Cover Image" v-model="editedBook.cover" @change="handleFileChange" accept="image/*"></v-file-input>
                    <v-btn type="submit" color="primary">{{ isEditMode ? 'Update' : 'Add' }}</v-btn>
                </v-form>
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
            isEditMode: false,
            bookId: null,
            dialog: false, // Define 'dialog' here
        };
    },
    methods: {
        openModal(book, mode) {
            this.editedBook = { ...book };
            this.isEditMode = mode === 'edit';
            this.bookId = book.id;
            this.dialog = true; // Open the modal by setting 'dialog' to true
        },
        async saveBook() {
            try {
                let response;
                if (this.isEditMode) {
                    response = await axios.put(`/api/books/${this.bookId}`, this.editedBook);
                } else {
                    response = await axios.post('/api/books', this.editedBook);
                }

                // Handle success
                console.log('Book saved:', response.data);
                this.dialog = false; // Close the modal by setting 'dialog' to false
            } catch (error) {
                // Handle error
                console.error('Error saving book:', error);
            }
        },
        handleFileChange(event) {
            this.editedBook.cover = event.target.files[0];
        },
        closeModal() {
            this.dialog = false; // Close the modal by setting 'dialog' to false
        },
    },
};
</script>
