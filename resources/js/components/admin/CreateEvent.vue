<script setup>
    import {ref} from "vue";
    import axios from "axios";
    import {format} from "date-fns";
    import {useToast} from "vue-toast-notification";

    let props = defineProps(['artists']);

    let dropdownIsOpen = ref(false);
    let artist = ref(null);
    let artistError = ref(null);
    let date = ref(null);
    let dateError = ref(null);
    let time = ref(null);
    let timeError = ref(null);

    const emit = defineEmits(['eventCreated']);

    function blurDateInput() {
        document.activeElement.blur();
    }

    function createEvent() {
        let passedValidation = true;
        artistError.value = dateError.value = timeError.value = null;

        if (artist.value === null) {
            artistError.value = 'Please select an artist';
            passedValidation = false;
        }

        if (date.value === null) {
            dateError.value = 'Please select a date';
            passedValidation = false;
        }

        if (time.value === null) {
            timeError.value = 'Please select a time';
            passedValidation = false;
        }

        if (passedValidation) {
            axios.post('/api/events', {
                data: {
                    attributes: {
                        start_at: `${format(date.value, 'yyyy-MM-dd')} ${time.value.label}:00`,
                        artist: {
                            data: artist.value.data
                        }
                    }
                }
            }).then(({data}) => {
                useToast().success('Successfully created', {
                    position: 'top-right',
                    duration: 4000
                });

                dropdownIsOpen.value = false;
                artist.value = null;
                date.value = null;
                time.value = null;

                emit('eventCreated', data);
            }).catch(error => {
                if (error.response.status === 400) {
                    artistError.value = error.response.data.errors.hasOwnProperty('artist')
                        ? error.response.data.errors.artist[0]
                        : null;
                    dateError.value = error.response.data.errors.hasOwnProperty('start_at')
                        ? error.response.data.errors.start_at[0]
                        : null;
                } else {
                    console.error('Unable to create event');
                }
            });
        }
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
                        <v-select v-model="artist" placeholder="Artist" :options="artists.map((artist) => ({label: artist.data.attributes.name, ...artist}))"></v-select>
                        <div v-if="artistError" class="text-red-600 font-bold px-4 pt-2">{{ artistError }}</div>
                    </div>
                    <div>
                        <button @click="dropdownIsOpen = !dropdownIsOpen" class="bg-blue-700 hover:bg-blue-800 ml-2 px-4 py-1 rounded text-white">Close</button>
                    </div>
                </div>
                <div class="pt-3">
                    <Datepicker v-model="date" placeholder="Date" inputFormat="EEEE do LLLL, yyyy" :clearable="true" :lowerLimit="new Date()" @closed="blurDateInput">
                        <template v-slot:clear="{ onClear }">
                            <button style="padding: 3px;" @click="onClear"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" class="align-middle" style="fill: rgba(60, 60, 60, 0.5);"><path d="M6.895455 5l2.842897-2.842898c.348864-.348863.348864-.914488 0-1.263636L9.106534.261648c-.348864-.348864-.914489-.348864-1.263636 0L5 3.104545 2.157102.261648c-.348863-.348864-.914488-.348864-1.263636 0L.261648.893466c-.348864.348864-.348864.914489 0 1.263636L3.104545 5 .261648 7.842898c-.348864.348863-.348864.914488 0 1.263636l.631818.631818c.348864.348864.914773.348864 1.263636 0L5 6.895455l2.842898 2.842897c.348863.348864.914772.348864 1.263636 0l.631818-.631818c.348864-.348864.348864-.914489 0-1.263636L6.895455 5z"></path></svg></button>
                        </template>
                    </Datepicker>
                    <div v-if="dateError" class="text-red-600 font-bold px-4 pt-2">{{ dateError }}</div>
                </div>
                <div class="py-3">
                    <v-select v-model="time" placeholder="Time" :options="Array.from({length: 48}, (x, i) => i + 1).map((i) => ({label: ((i / 2).toFixed(0) - 1).toString().padStart(2, '0') + ':' + (i % 2 ? '00' : '30')}))"></v-select>
                    <div v-if="timeError" class="text-red-600 font-bold px-4 pt-2">{{ timeError }}</div>
                </div>
                <div class="flex py-3">
                    <button @click="createEvent" class="mx-auto bg-red-600 hover:bg-red-700 px-8 py-2 rounded text-white">Add</button>
                </div>
            </div>
        </transition>
    </div>
</template>