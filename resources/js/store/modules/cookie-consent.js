import * as Cookie from "tiny-cookie";

const state = {
    consentCookie: null,
    consentCookieStatus: null,
};

const getters = {
    consentCookie: state => {
        return state.consentCookie;
    },
    consentCookieStatus: state => {
        return state.consentCookieStatus;
    }
}

const actions = {
    fetchConsentCookie({commit, state}) {
        commit('setConsentCookie', Cookie.get('cookie_consent'));
        commit('setConsentCookieStatus', 'success');
    },
    saveConsentCookie({commit, state}) {
        Cookie.set('cookie_consent', true, {expires: '6M'})
    }
};

const mutations = {
    setConsentCookie(state, consentCookie) {
        state.consentCookie = consentCookie;
    },
    setConsentCookieStatus(state, consentCookieStatus) {
        state.consentCookieStatus = consentCookieStatus;
    }
}

export default {
    state, getters, actions, mutations
};