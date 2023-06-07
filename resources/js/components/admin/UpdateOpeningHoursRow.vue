<script setup>
    let props = defineProps(['day']);

    function setTimes(event) {
        if (event.srcElement.checked) {
            props.day.data.attributes.open_at = null;
            props.day.data.attributes.close_at = null;
        } else {
            props.day.data.attributes.open_at = '00:00';
            props.day.data.attributes.close_at = '00:00';
        }
    }
</script>

<template>
    <div class="sm:flex">
        <div class="flex-none w-52 sm:text-right pr-2.5 text-lg">
            {{ day.data.attributes.day }}
        </div>
        <div class="grow flex">
            <div class="w-1/3 sm:text-center">
                <input type="checkbox" :checked="day.data.attributes.open_at === null || day.data.attributes.clos_at === null" @change="setTimes"> Closed
            </div>
            <div class="w-1/3 sm:text-center">
                <select v-if="day.data.attributes.open_at !== null && day.data.attributes.clos_at !== null" v-model="day.data.attributes.open_at" class="rounded-md border border-gray-500 px-4 h-8 focus:outline-none text-black">
                    <option v-for="i in 48" :value="((i / 2).toFixed(0) - 1).toString().padStart(2, '0') + ':' + (i % 2 ? '00' : '30')">{{ ((i / 2).toFixed(0) - 1).toString().padStart(2, '0') }}:{{ i % 2 ? '00' : '30' }}</option>
                </select>
            </div>
            <div class="w-1/3 sm:text-center">
                <select v-if="day.data.attributes.open_at !== null && day.data.attributes.clos_at !== null" v-model="day.data.attributes.close_at" class="rounded-md border border-gray-500 px-4 h-8 focus:outline-none text-black">
                    <option v-for="i in 48" :value="((i / 2).toFixed(0) - 1).toString().padStart(2, '0') + ':' + (i % 2 ? '00' : '30')">{{ ((i / 2).toFixed(0) - 1).toString().padStart(2, '0') }}:{{ i % 2 ? '00' : '30' }}</option>
                </select>
            </div>
        </div>
    </div>
</template>