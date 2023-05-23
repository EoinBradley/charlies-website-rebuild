import {createRouter, createWebHistory} from "vue-router";
import Home from "./views/Home.vue";

export default createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/', name: 'home', component: Home,
        }
    ],
    scrollBehavior() {
        return { top: 0, behavior: 'smooth' }
    },
});