<script setup>
    import {onMounted, ref} from "vue";
    import axios from "axios";
    import UpdateOpeningHoursRow from "../../components/admin/UpdateOpeningHoursRow.vue";
    import {useToast} from "vue-toast-notification";

    let loadingOpeningHours = ref(true);
    let openingHours = ref([]);
    let openingHoursError = ref(null);

    onMounted(() => {
        axios.get('/api/opening-hours')
            .then(({data}) => {
                openingHours.value = data.data
                loadingOpeningHours.value = false;
            })
            .catch(error => {
                console.error('Unable to get opening hours')
            })
    });

    function updateOpeningHours() {
        axios.put('/api/opening-hours', {
            days: openingHours.value.map(day => {
                return day.data.attributes;
            })
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
                debugger;
            } else {
                console.error('Unable to update opening hours');
            }
        })
    }
</script>

<template>
    <div>
        <h1 class="text-3xl font-bold py-10">UPDATE OPENING HOURS</h1>
        <div v-if="loadingOpeningHours" class="w-full text-center py-5">
            <font-awesome-icon :icon="['fas', 'spinner']" size="3x" spin />
        </div>
        <div v-else class="w-full py-4">
            <UpdateOpeningHoursRow v-for="(day, key) in openingHours" :key="key" :day="day" class="py-3" />
            <div class="flex py-3">
                <button @click="updateOpeningHours" class="mx-auto bg-red-600 hover:bg-red-700 px-8 py-2 rounded text-white">Update</button>
            </div>
        </div>
    </div>
</template>