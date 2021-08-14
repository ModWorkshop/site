export const state = () => ({
    user: false,
    categories: []
});

export const mutations = {
    setUser(state, user) {
        state.user = user;
    },
    setCategories(state, categories) {
        state.categories = categories;
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
    },
    categories(state) {
        return state.categories;
    }
};

export const actions = {
    async fetchCategories({commit}) {
        if (this.state.categories.length === 0) {
            const categories = await this.$axios.get('/categories').then(res => res.data);
            await commit('setCategories', categories);
        }
    },
    async nuxtServerInit({commit}, {req, $axios}) {
        try {
            const user = await $axios.get('/user').then(res => res.data);
            await commit('setUser', user);
        } catch (error) {
            //console.error(error);
        }
    }
};