<script setup>
    import {ref} from "vue";
    import {useStore} from "vuex";

    const store = useStore();

    let email = ref(null);
    let emailError = ref(null);
    let password = ref(null);
    let passwordError = ref(null);

    function login() {
        let data = {
            email: email.value,
            password: password.value,
        };

        password.value = null;

        axios.post('/api/login', data)
            .then(() => {
                store.dispatch('fetchAuthUser');
            })
            .catch(error => {
                if (error.response.status === 422) {
                    emailError.value = error.response.data.errors.hasOwnProperty('email')
                        ? error.response.data.errors.email[0]
                        : null;
                    passwordError.value = error.response.data.errors.hasOwnProperty('password')
                        ? error.response.data.errors.password[0]
                        : null;
                } else {
                    console.error('Unable to login user');
                }
            })
    }
</script>

<template>
    <div>
        <h1 class="text-3xl font-bold py-10">LOGIN</h1>
        <div class="w-full py-4">
            <div class="sm:flex py-3">
                <div class="flex-none w-52 sm:text-right pr-2.5 text-lg">
                    Email *
                </div>
                <div class="grow">
                    <input v-on:keyup.enter="login" type="email" v-model="email" placeholder="Email" class="rounded-md border border-gray-500 px-4 h-8 focus:outline-none w-full text-black" />
                    <div v-if="emailError" class="text-red-600 font-bold px-4 pt-2">{{ emailError }}</div>
                </div>
            </div>
            <div class="sm:flex py-3">
                <div class="flex-none w-52 sm:text-right pr-2.5 text-lg">
                    Password *
                </div>
                <div class="grow">
                    <input v-on:keyup.enter="login" type="password" v-model="password" placeholder="Password" class="rounded-md border border-gray-500 px-4 h-8 focus:outline-none w-full text-black" />
                    <div v-if="passwordError" class="text-red-600 font-bold px-4 pt-2">{{ passwordError }}</div>
                </div>
            </div>
            <div class="flex py-3">
                <button @click="login" class="mx-auto bg-red-600 hover:bg-red-700 px-8 py-2 rounded text-white">Login</button>
            </div>
        </div>
    </div>
</template>