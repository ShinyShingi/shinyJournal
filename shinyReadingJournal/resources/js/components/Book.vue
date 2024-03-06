<script setup>
import {defineProps, ref, onMounted, defineComponent, computed, watch} from 'vue';
import vue3StarRatings from "vue3-star-ratings";
import axios from 'axios';


const props = defineProps({
    book: Object
});
const emit = defineEmits(['editBook', 'removeBook', 'updateStatus']);

const rating = ref(0)

onMounted(()=>{
    if (props.book?.pivot?.rating){
        rating.value = props.book.pivot.rating;
    }
})

watch(rating,(newRating)=>{
    // console.log("rating changed, ", newRating)
})
const removeBook = () => {
    emit('removeBook', props.book.id);
};

const editBook = () => {
    console.log("editBook clicked")
    emit('editBook', props.book.id);
};
const updateRating = (newRating) => {
    console.log("updateRating called", props.book, newRating);
    axios.post(`/api/books/${props.book.id}/rate`, { rating: newRating })
        .then(response => {
            console.log(response.data.message);
            // Handle success
        })
        .catch(error => {
            console.error(error);
            // Handle error
        });
}
const computedRating = computed({
    get: () => rating.value,
    set: (value) => {
        rating.value = value;
        updateRating(value)
    }
});
const updateStatus = (newStatus) => {
    console.log("Initial book cover:", props.book.cover);
    console.log("read time: ", props.book.reat_at)

    emit('updateStatus', { id: props.book.id, status: newStatus });
};
const formatDate = (dateString) => {
    if (!dateString) return ''; // Handle null or undefined case

    const options = { year: 'numeric' , day: 'numeric', month: 'long' };
    return new Date(dateString).toLocaleDateString(undefined, options);
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
                <div class="details-container">
                    <h4 class="mt-5">{{ props.book.title }}</h4>
                    <h5>{{ props.book.author }}</h5>
                    <p class="series">{{ props.book.series }}</p>

                    <span>
                    Status:
                     <v-select
                         class="form-select mb-3 mt-2"
                         v-if="props.book && props.book.pivot"
                         v-model="props.book.pivot.status"
                         :items="['Unread', 'Reading', 'Read']"
                         @update:modelValue="updateStatus"
                     ></v-select>

                    <vue3-star-ratings
                            class="mb-3"
                            v-if="props.book && props.book.pivot && (props.book.pivot.status === 'Reading' || props.book.pivot.status === 'Read')"
                            v-model="computedRating"
                    />
                     <p v-if="props.book && props.book.pivot && props.book.pivot.status === 'Read'">
                          Read at: {{ formatDate(props.book.pivot.read_at) }}
                     </p>
                </span>
                    <v-btn variant="tonal" @click="removeBook" class="btn me-2 delete-book-btn">Remove</v-btn>
                    <v-btn variant="outlined" @click="editBook" class="btn me-2 btn-secondary edit-book-btn">
                        <i class="fa fa-pencil"></i>
                    </v-btn>
                </div>
            </div>
        </v-sheet>
    </v-col>

</template>
