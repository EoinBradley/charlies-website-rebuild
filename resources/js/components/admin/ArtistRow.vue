<script setup>
    import {onMounted, ref} from "vue";
    import {useToast} from "vue-toast-notification";
    import axios from "axios";

    let props = defineProps(['artist']);

    let dropdownIsOpen = ref(false);
    let artistName = ref('');
    let artistDescription = ref('');

    onMounted(() => {
        artistName.value = props.artist.data.attributes.name;
        artistDescription.value = props.artist.data.attributes.description;
    });

    function updateArtist() {
        axios.put(`/api/artists/${props.artist.data.id}`, {
            data: {
                ...props.artist.data,
                attributes: {
                    ...props.artist.data.attributes,
                    ...{
                        name: artistName.value,
                        description: artistDescription.value
                    }
                }
            }
        }).then(() => {
            useToast().success('Successfully updated', {
                position: 'top-right',
                duration: 4000
            });

            props.artist.data.attributes.name = artistName.value;
            props.artist.data.attributes.description = artistDescription.value;
            dropdownIsOpen.value = false;
        }).catch(error => {
            console.error('Unable to update artist');
        });
    }
</script>

<template>
    <div class="border border-gray-500 rounded-md p-4 my-2">
        <div class="flex">
            <div class="grow">
                <input v-if="dropdownIsOpen" type="text" v-model="artistName" placeholder="Name" class="rounded-md border border-gray-500 px-4 h-8 focus:outline-none w-full text-black" />
                <span v-else class="text-2xl font-bold">{{ artist.data.attributes.name }}</span>
            </div>
            <div>
                <button v-if="dropdownIsOpen" @click="dropdownIsOpen = !dropdownIsOpen" class="bg-blue-700 hover:bg-blue-800 ml-2 px-4 py-1 rounded text-white">Close</button>
                <button v-else @click="dropdownIsOpen = !dropdownIsOpen" class="bg-blue-700 hover:bg-blue-800 ml-2 px-4 py-1 rounded text-white">Edit</button>
            </div>
        </div>
        <transition
            enter-active-class="transition ease-out duration-100 transform"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75 transform"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95">
            <div v-show="dropdownIsOpen">
                <div class="py-3">
                    <textarea v-model="artistDescription" class="rounded-md border border-gray-500 focus:outline-none w-full h-56 p-4 text-black" placeholder="Description"></textarea>
                </div>
                <div class="flex py-3">
                    <button @click="updateArtist" class="mx-auto bg-red-600 hover:bg-red-700 px-8 py-2 rounded text-white">Update</button>
                </div>
            </div>
        </transition>
    </div>
</template>