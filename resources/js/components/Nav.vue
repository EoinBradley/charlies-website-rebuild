<script setup>
    import {computed, ref} from "vue";
    import axios from "axios";
    import {useStore} from "vuex";
    import {useRoute} from "vue-router";

    const store = useStore();
    const route = useRoute();

    let authUser = computed(() => store.getters.authUser);

    let dropdownIsOpen = ref(false);

    function removeFocusOnDropdown() {
        if (document.querySelector('#dropdown-wrapper').matches(':focus-within') === false) {
            dropdownIsOpen.value = false;
        }
    }

    window.onscroll = function () {
        if (route.fullPath === '/') {
            let clientTop = document.documentElement.clientTop;
            let scrollY = window.scrollY;

            let aboutUsPosition = document.querySelector('#about-us').getBoundingClientRect().top + scrollY - clientTop - 40;
            let eventsPosition = document.querySelector('#events').getBoundingClientRect().top + scrollY - clientTop - 40;
            let contactUsPosition = document.querySelector('#contact-us').getBoundingClientRect().top + scrollY - clientTop - 40;
            let videosPosition = document.querySelector('#videos').getBoundingClientRect().top + scrollY - clientTop - 40;

            document.querySelectorAll('.nav-item').forEach((link) => {
                link.classList.add('text-slate-400');
                link.classList.remove('text-slate-50');
            });

            if (scrollY >= aboutUsPosition && scrollY < eventsPosition) {
                document.querySelectorAll(".nav-item[data-nav='about-us']").forEach((link) => {
                    link.classList.add('text-slate-50');
                    link.classList.remove('text-slate-400');
                });
            } else if (scrollY >= eventsPosition && scrollY < contactUsPosition) {
                document.querySelectorAll(".nav-item[data-nav='events']").forEach((link) => {
                    link.classList.add('text-slate-50');
                    link.classList.remove('text-slate-400');
                });
            } else if (scrollY >= contactUsPosition && scrollY < videosPosition) {
                document.querySelectorAll(".nav-item[data-nav='contact-us']").forEach((link) => {
                    link.classList.add('text-slate-50');
                    link.classList.remove('text-slate-400');
                });
            } else if (scrollY >= videosPosition) {
                document.querySelectorAll(".nav-item[data-nav='videos']").forEach((link) => {
                    link.classList.add('text-slate-50');
                    link.classList.remove('text-slate-400');
                });
            }
        }
    }

    function scrollToSection(event) {
        document.getElementById(event.target.getAttribute("data-nav")).scrollIntoView({behavior: "smooth"});
    }

    function logout() {
        axios.post('/api/logout')
            .then(() => {
                dropdownIsOpen.value = false;
                store.dispatch('fetchAuthUser');
            })
            .catch(error => {
                console.error('Unable to logout user');
            });
    }

    function toggleDropdown(event) {
        event.preventDefault();

        dropdownIsOpen.value = !dropdownIsOpen.value

        document.getElementById('dropdown-wrapper').focus();
    }
</script>

<template>
    <nav class="fixed w-full bg-black z-10">
        <div class="mx-auto w-screen xl:w-[71rem] p-3 flex">
            <router-link to="/">
                <img src="/images/logo.png" class="w-32 flex-none"  alt="Charlie's bar cork"/>
            </router-link>
            <div class="text-right flex-auto hidden md:flex items-center justify-end">
                <div v-if="$route.fullPath === '/'">
                    <span class="text-slate-400 hover:text-slate-50 px-6 py-3 cursor-pointer nav-item" data-nav="about-us" @click="scrollToSection">About us</span>
                    <span class="text-slate-400 hover:text-slate-50 px-6 py-3 cursor-pointer nav-item" data-nav="events" @click="scrollToSection">Event guide</span>
                    <span class="text-slate-400 hover:text-slate-50 px-6 py-3 cursor-pointer nav-item" data-nav="contact-us" @click="scrollToSection">Contact</span>
                    <span class="text-slate-400 hover:text-slate-50 px-6 py-3 cursor-pointer nav-item" data-nav="videos" @click="scrollToSection">Videos</span>
                </div>
                <div v-else>
                    <router-link to="/" class="text-slate-400 hover:text-slate-50 px-6 py-3 cursor-pointer nav-item" active-class="text-slate-50" role="menuitem">
                        Home
                    </router-link>
                </div>
                <div v-if="authUser && authUser.data.roles.length > 0" id="dropdown-wrapper" @blur="removeFocusOnDropdown" tabindex="0" class="relative inline-block text-left z-10">
                    <router-link to="/admin" class="text-slate-400 hover:text-slate-50 px-6 py-3 cursor-pointer nav-item" active-class="text-slate-50" @click.native.capture="toggleDropdown">
                        Admin
                    </router-link>
                    <transition
                        enter-active-class="transition ease-out duration-100 transform"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75 transform"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95">
                        <div id="dropdown" v-show="dropdownIsOpen" @blur="removeFocusOnDropdown" tabindex="0" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y-2 divide-gray-300 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                            <div class="py-1" role="none">
                                <router-link to="/admin/account" v-on:click.native="dropdownIsOpen = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                                    Signed in as<br />
                                    <span class="font-bold break-words">{{ authUser.data.first_name }} {{ authUser.data.last_name }}</span>
                                </router-link>
                            </div>
                            <div v-if="authUser && authUser.data.roles.includes('Update homepage description')" class="py-1" role="none">
                                <router-link to="/admin/update-homepage-description" v-on:click.native="dropdownIsOpen = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 w-full text-left" role="menuitem">
                                    Update homepage description
                                </router-link>
                            </div>
                            <div class="py-1" role="none">
                                <button @click="logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 w-full text-left" role="menuitem">Logout</button>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
            <div class="flex flex-auto md:hidden items-center justify-end">
                <font-awesome-icon class="text-gray-400 px-6 cursor-pointer" @click="dropdownIsOpen = !dropdownIsOpen" :icon="['fas', 'bars']" size="2x" />
            </div>
        </div>
        <transition
            enter-active-class="transition ease-out duration-100 transform"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75 transform"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95">
            <div id="dropdown" v-show="dropdownIsOpen" class="grid md:hidden w-full text-white divide-y divide-gray-300 overflow-y-scroll max-h-96" tabindex="0" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                <div v-if="authUser && authUser.data.roles.length > 0" class="grid py-3" role="none">
                    <router-link to="/admin/account" v-on:click.native="dropdownIsOpen = false" class="text-slate-400 hover:text-slate-50 px-6 py-3 cursor-pointer nav-item" active-class="text-slate-50">
                        Signed in as<br />
                        <span class="font-bold break-words">{{ authUser.data.first_name }} {{ authUser.data.last_name }}</span>
                    </router-link>
                </div>
                <div class="grid py-3" role="none" v-if="$route.fullPath === '/'">
                    <span class="text-slate-400 hover:text-slate-50 px-6 py-3 cursor-pointer nav-item" data-nav="about-us" @click="scrollToSection">About us</span>
                    <span class="text-slate-400 hover:text-slate-50 px-6 py-3 cursor-pointer nav-item" data-nav="events" @click="scrollToSection">Event guide</span>
                    <span class="text-slate-400 hover:text-slate-50 px-6 py-3 cursor-pointer nav-item" data-nav="contact-us" @click="scrollToSection">Contact</span>
                    <span class="text-slate-400 hover:text-slate-50 px-6 py-3 cursor-pointer nav-item" data-nav="videos" @click="scrollToSection">Videos</span>
                </div>
                <div class="grid py-3" role="none" v-else>
                    <router-link to="/" v-on:click.native="dropdownIsOpen = false" class="text-slate-400 hover:text-slate-50 px-6 py-3 cursor-pointer nav-item" active-class="text-slate-50" role="menuitem">
                        Home
                    </router-link>
                </div>
                <div v-if="authUser && authUser.data.roles.includes('Update homepage description')" class="grid py-3" role="none">
                    <router-link to="/admin/update-homepage-description" v-on:click.native="dropdownIsOpen = false" class="text-slate-400 hover:text-slate-50 px-6 py-3 cursor-pointer nav-item" active-class="text-slate-50" role="menuitem">
                        Update homepage description
                    </router-link>
                </div>
                <div v-if="authUser && authUser.data.roles.length > 0" class="grid py-3" role="none">
                    <button @click="logout" class="text-slate-400 hover:text-slate-50 px-6 py-3 cursor-pointer nav-item w-full text-left" role="menuitem">Logout</button>
                </div>
            </div>
        </transition>
    </nav>
</template>