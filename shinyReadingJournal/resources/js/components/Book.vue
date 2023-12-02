<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    book: Object
});

const emit = defineEmits(['editBook', 'removeBook', 'updateStatus']);

const removeBook = () => {
    emit('removeBook', props.book.id);
};

const editBook = () => {
    emit('editBook', props.book.id);
};

const updateStatus = (newStatus) => {
    emit('updateStatus', { ...props.book, status: newStatus });
};
</script>

<template>
    <div class="book">
        <div class="image-container">
            <img :src="$getImageUrl(book.cover)" class="img-cover" alt="Cover Image">
        </div>
        <h4 class="mt-4">{{ props.book.title }}</h4>
        <h5>{{ props.book.author }}</h5>
        <p>{{ props.book.series }}</p>

        <span>
            Status:
            <select class="form-select mb-3 mt-2" v-model="props.book.status" @change="updateStatus($event.target.value)">
                <option value="unread">Unread</option>
                <option value="reading">Reading</option>
                <option value="read">Read</option>
            </select>
        </span>
        <button @click="removeBook" class="btn btn-danger me-2 delete-book-btn">Remove</button>
        <button @click="editBook" class="btn btn-secondary edit-book-btn">
            <i class="fa fa-pencil"></i>
        </button>
    </div>
</template>
