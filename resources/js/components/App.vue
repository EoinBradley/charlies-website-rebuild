<script setup>
    import PageFooter from "./PageFooter.vue";
    import Nav from "./Nav.vue";
    import {onMounted} from "vue";
    import {useStore} from "vuex";

    const store = useStore();

    onMounted(() => {
        store.dispatch('fetchAuthUser');
    });
</script>

<template>
    <div class="w-full">
        <Nav />
        <router-view :key="$route.fullPath"></router-view>
        <PageFooter />
    </div>
</template>
