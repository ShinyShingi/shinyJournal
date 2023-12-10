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
    console.log("editBook clicked")
    emit('editBook', props.book.id);
};

const updateStatus = (newStatus) => {
    console.log("Initial book cover:", props.book.cover);

    emit('updateStatus', { id: props.book.id, status: newStatus });
};



</script>
<template>
    <v-col
        v-for="n in 1"
        :key="n"
        cols="12"
        sm="12"
        md="4"
        lg="3"
        xl="2"
        xxl="2"
        align-self="center"
    >
        <v-sheet class="ma-2 pa-2">
            <div class="book">
                <div class="image-container">
                    <img :src="$getImageUrl(props.book.cover)" class="img-cover" :key="props.book.cover" alt="Cover Image">
                </div>
                <h4 class="mt-5">{{ props.book.title }}</h4>
                <h5>{{ props.book.author }}</h5>
                <p>{{ props.book.series }}</p>

                <span>
                    Status:
                     <v-select
                         class="form-select mb-3 mt-2"
                         v-model="props.book.status"
                         :items="['Unread', 'Reading', 'Read']"
                         @update:modelValue="updateStatus"
                     ></v-select>
                </span>
                <v-btn variant="tonal" @click="removeBook" class="btn me-2 delete-book-btn">Remove</v-btn>
                <v-btn variant="outlined" @click="editBook" class="btn me-2 btn-secondary edit-book-btn">
                    <i class="fa fa-pencil"></i>
                </v-btn>
            </div>
        </v-sheet>
    </v-col>

</template>
