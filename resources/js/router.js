import {createRouter, createWebHistory} from "vue-router";
import Home from "./views/Home.vue";
import Admin from "./views/admin/Index.vue";
import ManageAccount from "./views/admin/ManageAccount.vue";
import UpdateHomepageDescription from "./views/admin/UpdateHomepageDescription.vue";

let router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/', name: 'home', component: Home,
        },
        {
            path: '/admin', name: 'admin-login', component: Admin,
            children: [
                {
                    path: '', component: ManageAccount,
                },
                {
                    path: 'account', component: ManageAccount,
                },
                {
                    path: 'update-homepage-description', component: UpdateHomepageDescription,
                    meta: {permission: 'Update homepage description'}
                },
            ],
        },
    ],
    scrollBehavior() {
        return { top: 0, behavior: 'smooth' }
    },
});

export default router;