<script setup>
    import PageFooter from "./PageFooter.vue";
    import Nav from "./Nav.vue";
</script>

<template>
    <div class="w-full">
        <Nav />
        <router-view :key="$route.fullPath"></router-view>
        <PageFooter />
    </div>
</template>
