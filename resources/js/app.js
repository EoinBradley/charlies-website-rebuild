import { createApp } from 'vue';
import App from "./components/App.vue";
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faSquareFacebook } from '@fortawesome/free-brands-svg-icons';
import router from "./router";
import store from "./store";
import axios from 'axios';
import ToastPlugin from "vue-toast-notification";
import 'vue-toast-notification/dist/theme-sugar.css'
import vSelect from "vue-select"
import "vue-select/dist/vue-select.css";
import Datepicker from 'vue3-datepicker';

window.axios = axios;

const app = createApp(App);

function windowResize() {
    let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
}

window.onresize = windowResize;

windowResize();

app.use(router);

app.use(store);

app.use(ToastPlugin);

library.add(fas, faSquareFacebook)

app.component('font-awesome-icon', FontAwesomeIcon);

app.component('v-select', vSelect);

app.component('Datepicker', Datepicker);

app.mount('#app');
