<script setup>
    import AboutUs from "../components/AboutUs.vue";
    import EventGuide from "../components/EventGuide.vue";
    import ContactUs from "../components/ContactUs.vue";
    import Videos from "../components/Videos.vue";

    function moveToAboutUs() {
        document.getElementById('about-us').scrollIntoView({behavior: 'smooth'})
    }
</script>

<template>
    <div>
        <div class="h-screen w-screen text-center align-bottom table-cell" id="with-background">
            <font-awesome-icon @click="moveToAboutUs" class="text-white pb-10 cursor-pointer" :icon="['fas', 'chevron-down']" size="2x" />
        </div>
        <AboutUs class="w-full" id="about-us" />
        <EventGuide class="w-full bg-zinc-200" id="events" />
        <ContactUs class="w-full" id="contact-us" />
        <Videos class="w-full bg-zinc-200" id="videos" />
    </div>
</template>
