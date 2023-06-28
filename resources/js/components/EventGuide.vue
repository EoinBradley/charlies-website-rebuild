<script setup>
    import {onMounted, ref} from "vue";
    import axios from "axios";
    import {format, parseISO} from "date-fns";

    let loadingEvents = ref(true);
    let events = ref([]);
    let openedEventId = ref(null);

    onMounted(() => {
        axios.get('/api/upcoming-events')
            .then(({data}) => {
                events.value = data.data;
                loadingEvents.value = false;
            });
    })
</script>

<template>
    <div>
        <div class="mx-auto w-screen xl:w-[71rem] px-3 py-20 grid">
            <h1 class="text-3xl font-bold py-10">EVENT GUIDE</h1>
            <div v-if="loadingEvents" class="w-full text-center py-5">
                <font-awesome-icon :icon="['fas', 'spinner']" size="3x" spin />
            </div>
            <div v-else-if="events.length > 0" class="block">
                <div v-for="(event, key) in events" :key="event.data.id" @click="openedEventId = openedEventId !== event.data.id ? event.data.id : null" class="border border-gray-500 rounded-md p-4 my-4 bg-white cursor-pointer">
                    <div>
                        <span class="text-2xl font-bold">{{ event.data.attributes.artist.data.attributes.name }}</span><br />
                        <span class="text-lg text-slate-600">{{ format(parseISO(event.data.attributes.start_at), 'EEEE do LLLL, h:mm aaa') }}</span>
                    </div>
                    <transition
                        enter-active-class="transition ease-out duration-100 transform"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-100 transform"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95">
                        <div v-if="openedEventId === event.data.id">
                            <div class="mt-3 pt-3 border-t border-gray-500 whitespace-pre-line">
                                {{ event.data.attributes.artist.data.attributes.description }}
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
            <div v-else class="block">
                <div class="my-3">We don't have any more events to show at the moment</div>
            </div>
        </div>
    </div>
</template>
