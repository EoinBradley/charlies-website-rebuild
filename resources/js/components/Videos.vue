<script setup>

import YoutubeVideo from "./YoutubeVideo.vue";
</script>

<template>
    <div>
        <div class="mx-auto w-screen xl:w-[71rem] px-3 py-20 grid">
            <h1 class="text-3xl font-bold py-10">VIDEOS</h1>
            <div class="md:flex">
                <div class="md:w-1/2 w-full p-2">
                    <YoutubeVideo class="w-full" video-id="W4iI86OEFoI" />
                </div>
                <div class="md:w-1/2 w-full p-2">
                    <YoutubeVideo class="w-full" video-id="Wxn3b2nc18Y" />
                </div>
            </div>
            <div class="md:flex">
                <div class="md:w-1/2 w-full p-2">
                    <YoutubeVideo class="w-full" video-id="eBK75Hj4R-M" />
                </div>
                <div class="md:w-1/2 w-full p-2">
                    <YoutubeVideo class="w-full" video-id="lvxNu0mOxe8" />
                </div>
            </div>
            <div class="md:flex">
                <div class="md:w-1/2 w-full p-2">
                    <YoutubeVideo class="w-full" video-id="c6sVj2kqC7c" />
                </div>
                <div class="md:w-1/2 w-full p-2">
                    <YoutubeVideo class="w-full" video-id="CkZB3vmLXBk" />
                </div>
            </div>
            <div class="md:flex">
                <div class="md:w-1/2 w-full p-2">
                    <YoutubeVideo class="w-full" video-id="dbauyvM78mI" />
                </div>
                <div class="md:w-1/2 w-full p-2">
                    <YoutubeVideo class="w-full" video-id="V-u0I-WwJCY" />
                </div>
            </div>
        </div>
    </div>
</template>
