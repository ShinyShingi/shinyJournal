<script setup>
import {ref, nextTick, onMounted, computed} from 'vue';
import EditBookModal from "./AddEditBookModal.vue";
import BookComponent from "./Book.vue";
import axios from 'axios';
import { useRoute } from 'vue-router';


const route = useRoute();

const username = ref(route.params.username)

const editBookModal = ref(null);
const completedBooks = ref([]);
const incompleteBooks = ref([]);
const inProgressBooks = ref([]);

const totalWantToRead = computed(() => incompleteBooks.value.length);
const totalCurrentlyReading = computed(() => inProgressBooks.value.length);
const totalHaveRead = computed(() => completedBooks.value.length);

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


const addNewBook = () => {
  if (editBookModal.value) {
    editBookModal.value.openModal({
      title: '',
      author: '',
      series: '',
      cover: null,
      status: 'Unread'
    }, 'add');
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
        const response = await fetch(`/api/${username.value}/books`);

        if (response.ok) {
            const data = await response.json();

            // Categorize books based on their status in the pivot object
            completedBooks.value = data.books.filter(book => book.pivot.status === 'Read');
            incompleteBooks.value = data.books.filter(book => book.pivot.status === 'Unread');
            inProgressBooks.value = data.books.filter(book => book.pivot.status === 'Reading');

        } else {
            console.error('Failed to fetch books');
        }
    } catch (error) {
        console.error('Error fetching books:', error);
    }
};


const handleBookSaved = async (response) => {
    console.log('handleBookSaved triggered with:', response);

    let book;
    const { isNew } = response;

    if (isNew) {
        book = response.book.book;
        console.log("adding new book", book);
    } else {
        const { book: nestedBook } = response;
        book = nestedBook;
    }

    if (!book) {
        console.error('Book object is undefined:', response);
        return;
    }

    // Ensure that you're accessing the status from the pivot data
    const bookStatus = book.pivot ? book.pivot.status : book.status;

    if (isNew) {
        incompleteBooks.value.push(book);
        console.log('book added successfully:', book);
    } else {
        const updateList = (list) => {
            const index = list.value.findIndex(b => b.id === book.id);
            if (index !== -1) {
                list.value.splice(index, 1, book);
            }
        };

        switch (bookStatus) {
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

    await fetchBooks();

});
</script>


<template>
    <edit-book-modal ref="editBookModal" v-model:dialog="dialog" @book-saved="handleBookSaved"/>
    <v-container class="profileContainer">
      <h1> Hello, {{ username }},</h1>
      <h2>Here are your stats:</h2>
      <p>Books you want to read: {{ totalWantToRead }}</p>
      <p>Books you are reading: {{ totalCurrentlyReading }}</p>
      <p>Books you've read: {{ totalHaveRead }}</p>
      <h3>Do you want to <v-btn color="green darken-3" @click="addNewBook">
        Add New Book
      </v-btn>

      </h3>
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
                :showControls="true"
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
                :showControls="true"
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
                :showControls="true"
            />
        </v-row>
    </v-container>
</template>

<style>
.profileContainer {
  margin-top: 64px;
}
</style>
