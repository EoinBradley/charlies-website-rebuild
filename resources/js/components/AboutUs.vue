<script setup>
    import {onMounted, ref} from "vue";

    const description = window.data.homepage_description;

    let loadingOpeningHours = ref(true);
    let openingHours = ref([]);

    onMounted(() => {
        axios.get('/api/opening-hours')
            .then(({data}) => {
                openingHours.value = data.data;
                loadingOpeningHours.value = false;
            })
    });
</script>

<template>
    <div>
        <div class="mx-auto w-screen xl:w-[71rem] px-3 py-20 grid">
            <h1 class="text-3xl font-bold py-10">ABOUT US</h1>
            <div class="block">
                <img src="/images/outside-bar.jpg" class="w-screen md:float-right md:w-1/2 md:ml-3" alt="Outside bar">
                <div class="whitespace-pre-line pb-4">
                    {{ description }}
                </div>
                <div v-if="loadingOpeningHours" class="w-64 text-center py-5">
                    <font-awesome-icon :icon="['fas', 'spinner']" size="3x" spin />
                </div>
                <ul v-else>
                    <li v-for="day in openingHours" class="flex py-2">
                        <div class="w-32">{{ day.day }}</div>
                        <div>
                            <span v-if="day.openAt === null || day.closeAt === null">Closed</span>
                            <span v-else>{{ day.openAt }} - {{ day.closeAt }}</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
