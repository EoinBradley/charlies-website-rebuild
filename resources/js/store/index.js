import {createStore} from "vuex";
import user from "./modules/user";
import cookieConsent from "./modules/cookie-consent";

export default createStore({
    modules: {
        user,
        cookieConsent,
    }
});