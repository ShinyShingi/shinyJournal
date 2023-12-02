<script setup>
import {ref, nextTick, onMounted} from 'vue';
import EditBookModal from "./AddEditBookModal.vue";
import BookComponent from "./Book.vue";
const editBookModal = ref(null);


const completedBooks = ref([]);
const incompleteBooks = ref([]);
const inProgressBooks = ref([]);

const removeBook = (id) => {
    // Logic to remove the book
    if (confirm('Are you sure you want to delete this book?')) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/updateBook/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                incompleteBooks.value = incompleteBooks.value.filter(book => book.id !== id);
                completedBooks.value = completedBooks.value.filter(book => book.id !== id);
                inProgressBooks.value = inProgressBooks.value.filter(book => book.id !== id);
            })
            .catch((error) => console.error('Error:', error));
    }
};
const editBook = async (id) => {
    git
    // Logic to edit the book
    fetch(`/getBook/${id}`)
        .then(response => response.json())
        .then(async data => {
            if (editBookModal.value) {
                editBookModal.value.openModal(data);
            }
        })
        .catch((error) => console.error('Error:', error));
};

const updateStatus = (book) => {
    // Logic to handle status update
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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

            // Remove the book from all lists
            incompleteBooks.value = incompleteBooks.value.filter(b => b.id !== book.id);
            completedBooks.value = completedBooks.value.filter(b => b.id !== book.id);
            inProgressBooks.value = inProgressBooks.value.filter(b => b.id !== book.id);

            // Add the book to the correct list based on its new status
            if (book.status === 'unread') {
                incompleteBooks.value.push(book);
            } else if (book.status === 'reading') {
                inProgressBooks.value.push(book);
            } else if (book.status === 'read') {
                completedBooks.value.push(book);
            }
        })
        .catch((error) => console.error('Error:', error));

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
});
</script>


<template>
    <edit-book-modal ref="editBookModal"/>
    <div class="container">
        <h1 class="mb-5 mt-3">My Books</h1>
        <div>
            <h3 class="mb-3">Books to read:</h3>
            <ul class="row ps-0" id="incomplete-books">
                <li v-for="book in incompleteBooks" :key="book.id" class="mt-3 mb-1 col-12 col-md-3 book">
                    <book-component :book="book" @editBook="editBook" @removeBook="removeBook" @updateStatus="updateStatus" />
                </li>
            </ul>
        </div>
        <div class="">
            <h3 class="mb-3">Books in progress:</h3>
            <ul class="row" id="in-progress-books">
                <li v-for="book in inProgressBooks" :key="book.id" class="mt-3 mb-1 col-12 col-md-3 book">
                    <book-component :book="book" @editBook="editBook" @removeBook="removeBook" @updateStatus="updateStatus" />
                </li>
            </ul>
        </div>
        <div class="">
            <h3 class="mb-3 mt-5">Completed Books:</h3>
            <ul class="mb-5 row" id="completed-books">
                <li v-for="book in completedBooks" :key="book.id" class="mt-3 mb-1 col-12 col-md-3 book">
                    <book-component :book="book" @editBook="editBook" @removeBook="removeBook" @updateStatus="updateStatus" />
                </li>
            </ul>
        </div>
    </div>

</template>
