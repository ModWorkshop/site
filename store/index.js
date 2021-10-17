import { defineStore } from 'pinia';

export const useStore = defineStore('main', {
    state: () => ({
        user: false,
        games: [],
        tags: [],
        counter: 1,
    }),
    getters: {
        hasPermission(state) {
            const permissions = state.user && state.user.permissions;
            if (permissions) { //This is cached, basically if no permissions, we never have any permissions! Duh.
                return () => false;
            } else {
                return perm => permissions[perm] === true;
            }
        }
    },
    actions: {
        async fetchGames() {
            if (this.games.length === 0) {
                const { $axios } = this.$nuxt;
                const { data: games } = await $axios.get('/games');
                this.games = games;
            }
        },
        async nuxtServerInit() {
            try {
                const { $axios } = this.$nuxt;
                const { data: user } = await $axios.get('/user');
                this.user = user;
            } catch (error) {
                console.error(error.message);
            }
        },
        setUserAvatar(avatar) {
            this.user.avatar = avatar;
        }
    }
});