<script setup>
    import {onMounted, ref} from "vue";
    import {format, parseISO} from "date-fns";
    import {useToast} from "vue-toast-notification";
    import axios from "axios";

    let props = defineProps(['event']);

    let dropdownIsOpen = ref(false);
    let date = ref(null);
    let dateError = ref(null);
    let time = ref(null);
    let timeError = ref(null);

    const emit = defineEmits(['eventCancelled']);

    onMounted(() => {
        date.value = parseISO(props.event.data.attributes.start_at);
        time.value = {
            label: format(parseISO(props.event.data.attributes.start_at), 'HH:mm')
        };
    });

    function updateEvent() {
        let passedValidation = true;
        dateError.value = timeError.value = null;

        if (date.value === null) {
            dateError.value = 'Please select a date';
            passedValidation = false;
        }

        if (time.value === null) {
            timeError.value = 'Please select a time';
            passedValidation = false;
        }

        if (passedValidation) {
            axios.put(`/api/events/${props.event.data.id}`, {
                data: {
                    ...props.event.data,
                    attributes: {
                        ...props.event.data.attributes,
                        ...{
                            start_at: `${format(date.value, 'yyyy-MM-dd')} ${time.value.label}:00`,
                        }
                    }
                }
            }).then(() => {
                useToast().success('Successfully updated', {
                    position: 'top-right',
                    duration: 4000
                });

                props.event.data.attributes.start_at = `${format(date.value, 'yyyy-MM-dd')} ${time.value.label}:00`;
                dropdownIsOpen.value = false;
            }).catch(error => {
                if (error.response.status === 400) {
                    dateError.value = error.response.data.errors.hasOwnProperty('start_at')
                        ? error.response.data.errors.start_at[0]
                        : null;
                } else {
                    console.error('Unable to create event');
                }
            });
        }
    }

    function cancelEvent() {
        axios.delete(`/api/events/${props.event.data.id}`)
            .then(() => {
                useToast().success('Successfully updated', {
                    position: 'top-right',
                    duration: 4000
                });

                dropdownIsOpen.value = false;
                emit('eventCancelled', props.event);
            })
            .catch(error => {
                console.error('Unable to cancel event');
            })
    }

    function blurDateInput() {
        document.activeElement.blur();
    }
</script>

<template>
    <div class="border border-gray-500 rounded-md p-4 my-2">
        <div class="flex">
            <div class="grow">
                <input v-if="dropdownIsOpen" type="text" :value="event.data.attributes.artist.data.attributes.name" class="rounded-md border border-gray-500 px-4 h-8 focus:outline-none w-full text-black cursor-not-allowed" disabled>
                <div v-else>
                    <span class="text-2xl font-bold">{{ event.data.attributes.artist.data.attributes.name }}</span><br />
                    <span class="text-lg text-slate-600">{{ format(parseISO(event.data.attributes.start_at), 'EEEE do LLLL, h:mm aaa') }}</span>
                </div>
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
            <div v-if="dropdownIsOpen">
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
                    <button @click="cancelEvent" class="mx-auto px-8 py-2 rounded text-red-600 hover:text-red-700">Cancel</button>
                    <button @click="updateEvent" class="mx-auto bg-red-600 hover:bg-red-700 px-8 py-2 rounded text-white">Update</button>
                </div>
            </div>
        </transition>
    </div>
</template>