<script setup>
    import {useStore} from "vuex";
    import {computed, ref} from "vue";

    const store = useStore();

    let authUser = computed(() => store.getters.authUser);

    let emailError = ref(null);
</script>

<template>
    <div>
        <h1 class="text-3xl font-bold py-10">ACCOUNT</h1>
        <div class="w-full py-4">
            <div class="sm:flex py-3">
                <div class="flex-none w-52 sm:text-right pr-2.5 text-lg">
                    Name
                </div>
                <div class="grow text-lg">
                    {{ authUser.data.attributes.first_name }} {{ authUser.data.attributes.last_name }}
                </div>
            </div>
            <div class="sm:flex py-3">
                <div class="flex-none w-52 sm:text-right pr-2.5 text-lg">
                    Email *
                </div>
                <div class="grow">
                    <input type="email" v-model="authUser.data.attributes.email" placeholder="Email" class="rounded-md border border-gray-500 px-4 h-8 focus:outline-none w-full text-black cursor-not-allowed" disabled />
                    <div v-if="emailError" class="text-red-600 font-bold px-4 pt-2">{{ emailError }}</div>
                </div>
            </div>
            <div class="flex py-3">
                <button class="mx-auto bg-red-600 hover:bg-red-700 px-8 py-2 rounded text-white cursor-not-allowed">Update</button>
            </div>
        </div>
    </div>
</template>
