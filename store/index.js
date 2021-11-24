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
        /**
         * Fetches the tags for quick use around the site
         */
        async fetchTags() {
            if (this.tags.length === 0) {
                const { $ftch } = this.$nuxt;
                this.tags = await $ftch.get('/tags');
            }
        },
        async fetchGames() {
            if (this.games.length === 0) {
                const { $ftch } = this.$nuxt;
                this.games = await $ftch.get('/games');
            }
        },
        async nuxtServerInit() {
            try {
                const { $ftch } = this.$nuxt;
                this.user = await $ftch.get('/user');
            } catch (error) {
                console.error(error.message);
            }
        },
        setUserAvatar(avatar) {
            this.user.avatar = avatar;
        }
    }
});