<script setup>
    import {onMounted, ref} from "vue";
    import axios from "axios";
    import {useToast} from "vue-toast-notification";

    let loadingHomepageDescription = ref(true);
    let homepageDescription = ref('');
    let homepageDescriptionError = ref(null);

    onMounted(() => {
        axios.get('/api/homepage-description')
            .then(({data}) => {
                homepageDescription.value = data.data.description;
                loadingHomepageDescription.value = false;
            })
            .catch(error => {
                console.error('Unable to get homepage description');
            });
    });

    function updateDescription() {
        axios.put('/api/homepage-description', {
            description: homepageDescription.value.value
        }).then(() => {
            useToast().success('Successfully updated', {
                position: 'top-right',
                duration: 4000
            });

            window.scrollTo({
                top: 0,
                left: 0,
                behavior: "smooth",
            });
        }).catch(error => {
            if (error.response.status === 422) {
                homepageDescriptionError.value = error.response.data.errors.hasOwnProperty('description')
                    ? error.response.data.errors.description[0]
                    : null;
            } else {
                console.error('Unable to update homepage description');
            }
        });
    }
</script>

<template>
    <div>
        <h1 class="text-3xl font-bold py-10">UPDATE HOMEPAGE DESCRIPTION</h1>
        <div v-if="loadingHomepageDescription" class="w-full text-center py-5">
            <font-awesome-icon :icon="['fas', 'spinner']" size="3x" spin />
        </div>
        <div v-else class="w-full py-4">
            <div class="py-3">
                <textarea v-model="homepageDescription.value" class="rounded-md border border-gray-500 focus:outline-none w-full h-56 p-4 text-black"></textarea>
                <div v-if="homepageDescriptionError" class="text-red-600 font-bold px-4 pt-2">{{ homepageDescriptionError }}</div>
            </div>
            <div class="flex py-3">
                <button @click="updateDescription" class="mx-auto bg-red-600 hover:bg-red-700 px-8 py-2 rounded text-white">Update</button>
            </div>
        </div>
    </div>
</template>