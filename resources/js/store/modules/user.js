const state = {
    user: null,
    userStatus: null,
};

const getters = {
    authUser: state => {
        return state.user;
    },
    authUserStatus: state => {
        return state.userStatus;
    }
};

const actions = {
    fetchAuthUser({commit, state}) {
        commit('setAuthUserStatus', 'loading');

        axios.get('/api/auth-user')
            .then(res => {
                commit('setAuthUser', res.data);
                commit('setAuthUserStatus', 'success');
            })
            .catch(error => {
                if (error.response.status === 401) {
                    commit('setAuthUser', null);
                    commit('setAuthUserStatus', 'success');
                } else {
                    console.error('Unable to fetch auth user');
                    commit('setAuthUserStatus', 'error');
                }
            });
    }
};

const mutations = {
    setAuthUser(state, user) {
        state.user = user;
    },
    setAuthUserStatus(state, status) {
        state.userStatus = status;
    }
};

export default {
    state, getters, actions, mutations
};
