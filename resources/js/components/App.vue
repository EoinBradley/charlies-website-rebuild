<script setup>
    import PageFooter from "./PageFooter.vue";
    import Nav from "./Nav.vue";
    import {onMounted} from "vue";
    import {useStore} from "vuex";
    import CookieConsentBanner from "./CookieConsentBanner.vue";

    const store = useStore();

    onMounted(() => {
        store.dispatch('fetchAuthUser');
        store.dispatch('fetchConsentCookie');
    });
</script>

<template>
    <div class="w-full">
        <Nav />
        <router-view :key="$route.fullPath"></router-view>
        <PageFooter />
        <CookieConsentBanner />
    </div>
</template>
