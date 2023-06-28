<script setup>
    import {onMounted, ref} from "vue";
    import CreateEvent from "../../components/admin/CreateEvent.vue";
    import axios from "axios";
    import EventRow from "../../components/admin/EventRow.vue";

    let loadingArtists = ref(true);
    let artists = ref([]);
    let loadingEvents = ref(true);
    let events = ref([]);

    onMounted(() => {
        axios.get('/api/artists')
            .then(({data}) => {
                artists.value = data.data;
                loadingArtists.value = false;
            })
            .catch(error => {
                console.error('Unable to get artists')
            });

        axios.get('/api/upcoming-events')
            .then(({data}) => {
                events.value = data.data;
                loadingEvents.value = false;
            });
    });

    function addEvent(event) {
        events.value = [
            event,
            ...events.value
        ];
    }
</script>

<template>
    <div>
        <h1 class="text-3xl font-bold py-10">MANAGE EVENTS</h1>
        <div v-if="loadingArtists || loadingEvents" class="w-full text-center py-5">
            <font-awesome-icon :icon="['fas', 'spinner']" size="3x" spin />
        </div>
        <div v-else class="w-full py-4">
            <CreateEvent class="w-full" :artists="artists" @event-created="addEvent" />
            <EventRow v-for="(event, key) in events" :key="event.data.id" :event="event" class="w-full" />
        </div>
    </div>
</template>