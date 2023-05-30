<script setup>
    import Login from "./Login.vue";
    import {useStore} from "vuex";
    import {computed} from "vue";

    const store = useStore();

    let authUserStatus = computed(() => store.getters.authUserStatus);
    let authUser = computed(() => store.getters.authUser);
</script>

<template>
    <div>
        <div class="mx-auto w-screen xl:w-[71rem] px-3 py-20 grid">
            <div v-if="authUserStatus !== null && authUserStatus !== 'loading'">
                <router-view v-if="authUser !== null" :key="$route.fullPath" />
                <Login v-else />
            </div>
        </div>
    </div>
</template>