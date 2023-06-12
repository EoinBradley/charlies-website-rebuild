<script setup>
    import {onMounted, ref} from "vue";
    import axios from "axios";
    import ArtistRow from "../../components/admin/ArtistRow.vue";

    let loadingArtists = ref(true);
    let artists = ref([]);

    onMounted(() => {
        axios.get('/api/artists')
            .then(({data}) => {
                artists.value = data.data;
                loadingArtists.value = false;
            })
            .catch(error => {
                console.error('Unable to get artists')
            });
    });
</script>

<template>
    <div>
        <h1 class="text-3xl font-bold py-10">MANAGE ARTISTS</h1>
        <div v-if="loadingArtists" class="w-full text-center py-5">
            <font-awesome-icon :icon="['fas', 'spinner']" size="3x" spin />
        </div>
        <div v-else class="w-full py-4">
            <ArtistRow v-for="(artist, key) in artists" :key="artist.data.id" :artist="artist" class="w-full" />
        </div>
    </div>
</template>