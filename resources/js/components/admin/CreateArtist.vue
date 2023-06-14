<script setup>
    import {ref} from "vue";
    import axios from "axios";
    import {useToast} from "vue-toast-notification";

    let dropdownIsOpen = ref(false);
    let artistName = ref('');
    let artistDescription = ref('');

    const emit = defineEmits(['artistCreated']);

    function createArtist() {
        axios.post('/api/artists', {
            data: {
                attributes: {
                    name: artistName.value,
                    description: artistDescription.value
                }
            }
        }).then(({data}) => {
            useToast().success('Successfully created', {
                position: 'top-right',
                duration: 4000
            });

            dropdownIsOpen.value = false;
            artistName.value = '';
            artistDescription.value = '';

            emit('artistCreated', data);
        }).catch(error => {
            console.error('Unable to create artist');
        });
    }
</script>

<template>
    <div class="inline-block">
        <button v-if="dropdownIsOpen === false" @click="dropdownIsOpen = !dropdownIsOpen" class="bg-blue-700 hover:bg-blue-800 px-6 py-1 rounded text-white float-right"><font-awesome-icon :icon="['fas', 'plus']" /></button>
        <transition
            enter-active-class="transition ease-out duration-100 transform"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75 transform"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95">
            <div v-if="dropdownIsOpen" class="border border-gray-500 rounded-md p-4 my-2">
                <div class="flex">
                    <div class="grow">
                        <input v-model="artistName" type="text" placeholder="Name" class="rounded-md border border-gray-500 px-4 h-8 focus:outline-none w-full text-black" />
                    </div>
                    <div>
                        <button @click="dropdownIsOpen = !dropdownIsOpen" class="bg-blue-700 hover:bg-blue-800 ml-2 px-4 py-1 rounded text-white">Close</button>
                    </div>
                </div>
                <div class="py-3">
                    <textarea v-model="artistDescription" class="rounded-md border border-gray-500 focus:outline-none w-full h-56 p-4 text-black" placeholder="Description"></textarea>
                </div>
                <div class="flex py-3">
                    <button @click="createArtist" class="mx-auto bg-red-600 hover:bg-red-700 px-8 py-2 rounded text-white">Add</button>
                </div>
            </div>
        </transition>
    </div>
</template>