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
                    <v-file-input label="Cover Image" v-model="selectedCoverFile" @change="handleFileChange" accept="image/*"></v-file-input>
                    <input type="text" v-if="selectedCoverFile" :value="selectedCoverFile.name" readonly>
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
            selectedCoverFile: null, // Store the selected file
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
                const formData = new FormData();
                formData.append('title', this.editedBook.title);
                formData.append('author', this.editedBook.author);
                formData.append('series', this.editedBook.series);

                // Check if 'cover' is not null and add it to formData if it exists
                if (this.editedBook.cover) {
                    formData.append('cover', this.editedBook.cover);
                }

                let response;
                if (this.isEditMode) {
                    response = await axios.put(`/api/books/${this.bookId}`, formData);
                } else {
                    response = await axios.post('/api/books', formData);
                }

                // Handle success
                console.log('Book saved:', response.data);
                // Close the modal here
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
