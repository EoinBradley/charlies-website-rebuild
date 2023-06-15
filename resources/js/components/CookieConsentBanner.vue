<script setup>
    import {useStore} from "vuex";
    import {computed} from "vue";

    const store = useStore();

    let consentCookieStatus = computed(() => store.getters.consentCookieStatus);
    let consentCookie = computed(() => store.getters.consentCookie);

    function acceptCookies() {
        store.dispatch('saveConsentCookie');
        store.dispatch('fetchConsentCookie');
    }
</script>

<template>
    <transition appear>
        <div v-if="consentCookieStatus === 'success' && consentCookie === null" class="fixed bottom-4 left-4 p-6 bg-black text-white w-80 rounded-md text-center">
            This website uses cookies to improve your experience.
            <a aria-label="learn more about cookies" role="button" tabindex="0" class="underline" href="https://www.cookiesandyou.com" rel="noopener noreferrer nofollow" target="_blank">Learn more</a>
            <div class="w-full flex pt-4">
                <button @click="acceptCookies" class="mx-auto bg-red-600 hover:bg-red-700 px-8 py-2 rounded text-white">Got it!</button>
            </div>
        </div>
    </transition>
</template>