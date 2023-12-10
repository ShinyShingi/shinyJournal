<script setup>
import {ref, nextTick, onMounted, defineProps} from 'vue';
import EditBookModal from "./AddEditBookModal.vue";
import BookComponent from "./Book.vue";
import axios from 'axios';

const editBookModal = ref(null);
const completedBooks = ref([]);
const incompleteBooks = ref([]);
const inProgressBooks = ref([]);


const dialog = ref(false);
const removeBook = (id) => {
    if (confirm('Are you sure you want to delete this book?')) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        axios
            .delete(`/updateBook/${id}`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
            })
            .then((response) => {
                if (response.data.success) {
                    // Book successfully deleted, update book lists
                    incompleteBooks.value = incompleteBooks.value.filter(book => book.id !== id);
                    completedBooks.value = completedBooks.value.filter(book => book.id !== id);
                    inProgressBooks.value = inProgressBooks.value.filter(book => book.id !== id);
                } else {
                    console.error('Failed to remove book');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
    }
};

const editBook = async (id) => {
    console.log("I'm in editBook")
    // Logic to edit the book
    fetch(`/getBook/${id}`)
        .then(response => response.json())
        .then(async data => {
            if (editBookModal.value) {
                editBookModal.value.openModal(data, 'edit');  // Pass 'edit' as mode
            }
        })
        .catch((error) => console.error('Error:', error));
};


const updateStatus = (book) => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log( "Here is the book data", book)
    fetch(`/updateStatus/${book.id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status: book.status })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Server responded with an error!');
            }
            return response.json();
        })
        .then(data => {
            console.log('Success:', data);

            // Create a new updated book object (assuming 'data' contains the updated book)
            const bookData = { ...book, ...data.book };
            console.log('Updated book:', bookData);

            // Remove the book from all lists
            removeBookFromAllLists(book.id);

            // Add the updated book to the correct list based on its new status
            addBookToCorrectList(bookData);
        })
        .catch((error) => console.error('Error:', error));
};

const removeBookFromAllLists = (bookId) => {
    incompleteBooks.value = incompleteBooks.value.filter(b => b.id !== bookId);
    completedBooks.value = completedBooks.value.filter(b => b.id !== bookId);
    inProgressBooks.value = inProgressBooks.value.filter(b => b.id !== bookId);
};

const addBookToCorrectList = (bookData) => {
    if (bookData.status === 'Unread') {
        incompleteBooks.value.push(bookData);
    } else if (bookData.status === 'Reading') {
        inProgressBooks.value.push(bookData);
    } else if (bookData.status === 'Read') {
        completedBooks.value.push(bookData);
    }
};
const fetchBooks = async () => {
    try {
        const response = await fetch(`/api/books`);
        if (response.ok) {
            const data = await response.json();
            completedBooks.value = data.completedBooks;
            incompleteBooks.value = data.incompleteBooks;
            inProgressBooks.value = data.inProgressBooks;
        } else {
            console.error('Failed to fetch books');
        }
    } catch (error) {
        console.error('Error fetching books:', error);
    }
};

const handleBookSaved = async (response) => {
    console.log('handleBookSaved triggered with:', response);

    const { book, isNew } = response;

    if (isNew) {
        // Handle the addition of a new book
        switch (book.status) {
            case 'Unread':
                incompleteBooks.value.push(book);
                break;
            case 'Reading':
                inProgressBooks.value.push(book);
                break;
            case 'Read':
                completedBooks.value.push(book);
                break;
            default:
                console.error('Unknown book status:', book.status);
        }
    } else {
        // Handle updating an existing book
        const updateList = (list) => {
            const index = list.value.findIndex(b => b.id === book.id);
            if (index !== -1) {
                list.value.splice(index, 1, book);
            }
        };

        switch (book.status) {
            case 'Unread':
                updateList(incompleteBooks);
                break;
            case 'Reading':
                updateList(inProgressBooks);
                break;
            case 'Read':
                updateList(completedBooks);
                break;
        }
    }

    // Optional: Refresh the books list from the server
    await fetchBooks();
};


onMounted(async () => {
    const response = await fetch(`/api/books`);

    if (response.ok) {
        const data = await response.json();
        completedBooks.value = data.completedBooks;
        incompleteBooks.value = data.incompleteBooks;
        inProgressBooks.value = data.inProgressBooks;
    } else {
        // Handle error
        console.error('Failed to fetch books');
    }
    await fetchBooks();

});
</script>


<template>
    <edit-book-modal ref="editBookModal" v-model:dialog="dialog" @book-saved="handleBookSaved"/>
    <v-container>
        <h1 class="">My Books</h1>
        <h3 class="ma-2">Books to read:</h3>

        <v-row class="">
            <book-component
                v-for="book in incompleteBooks"
                :key="book.id"
                :book="book"
                @edit-book="editBook"
                @remove-book="removeBook"
                @updateStatus="updateStatus"
            />

        </v-row>

        <h3  class="ma-2">Books in progress:</h3>
        <v-row class="">
            <book-component
                v-for="book in inProgressBooks"
                :key="book.id"
                :book="book"
                @edit-book="editBook"
                @remove-book="removeBook"
                @updateStatus="updateStatus"
            />
        </v-row>

        <h3  class="ma-2">Completed Books:</h3>
        <v-row>
            <book-component
                v-for="book in completedBooks"
                :key="book.id"
                :book="book"
                @edit-book="editBook"
                @remove-book="removeBook"
                @updateStatus="updateStatus"
            />
        </v-row>
    </v-container>
</template>

