<script setup>
    let props = defineProps(['videoId']);
</script>

<template>
    <div>
        <iframe class="w-full aspect-video" :src="`https://www.youtube.com/embed/${videoId}`" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen />
    </div>
</template>