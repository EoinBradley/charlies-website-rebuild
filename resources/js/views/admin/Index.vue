<script setup>
    import Login from "./Login.vue";
    import {useStore} from "vuex";
    import {computed} from "vue";
    import InsufficientPermissions from "../../components/InsufficientPermissions.vue";

    const store = useStore();

    let authUserStatus = computed(() => store.getters.authUserStatus);
    let authUser = computed(() => store.getters.authUser);
</script>

<template>
    <div>
        <div class="mx-auto w-screen xl:w-[71rem] px-3 py-20 grid">
            <div v-if="authUserStatus !== null && authUserStatus !== 'loading'">
                <Login v-if="authUser === null" />
                <InsufficientPermissions v-else-if="'permission' in $route.meta && authUser.data.attributes.roles.includes($route.meta.permission) === false" />
                <router-view v-else :key="$route.fullPath" />
            </div>
        </div>
    </div>
</template>