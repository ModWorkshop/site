export const state = () => ({
    user: false,
    games: [],
    tags: []
});

export const mutations = {
    setUser(state, user) {
        state.user = user;
    },
    setGames(state, games) {
        state.games = games;
    },
    setUserAvatar(state, avatar) {
        state.user.avatar = avatar;
    }
};

export const getters = {
    userId(state) {
        return state.user.id;
    },
    hasPermission: state => function(perm) {
        return state.user?.permissions[perm] === true;
    }
};

export const actions = {
    async fetchGames({commit}) {
        if (this.state.games.length === 0) {
            const games = await this.$axios.get('/games').then(res => res.data);
            await commit('setGames', games);
        }
    },
    async nuxtServerInit({commit}, {$axios}) {
        try {
            const user = await $axios.get('/user').then(res => res.data);
            await commit('setUser', user);
        } catch (error) {
            //console.error(error);
        }
    }
};