export const state = () => ({
    user: false
});

export const mutations = {
    setUser(state, user) {
        state.user = user;
    },
    setUserAvatar(state, avatar) {
        state.user.avatar = avatar;
    }
};

export const getters = {
    user(state) {
        return state.user;
    },
    userAvatar(state) {
        return 'http://localhost:8001/storage/' + state.user.avatar;
    },
    userId(state) {
        return state.user.id;
    }
};

export const actions = {
    async nuxtServerInit({commit}, {req, $axios}) {
        try {
            const user = await $axios.get('/user').then(res => res.data);
            await commit('setUser', user);
        } catch (error) {
            //console.error(error);
        }
    }
};