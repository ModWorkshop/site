import { defineStore } from 'pinia';
import { reloadToken } from '~~/utils/helpers';

export const useStore = defineStore('main', {
    state: () => ({
        csrf: '',
        user: false,
        games: [],
        tags: [],
        counter: 1,
    }),
    getters: {
        hasPermission(state) {
            const permissions = state.user && state.user.permissions;
            if (!permissions) { //This is cached, basically if no permissions, we never have any permissions! Duh.
                return () => false;
            } else {
                return perm => permissions[perm] === true;
            }
        }
    },
    actions: {
        /**
         * Attempts to login the user (automatically)
         */
        async attemptLoginUser() {
            const userData = await useGet('/user');
            this.user = userData;
            
            const router = useRouter();
            await reloadToken();
            router.push('/');
        },
        /**
         * Fetches the tags for quick use around the site
         */
        async fetchTags() {
            if (this.tags.length === 0) {
                this.tags = await useGet('/tags');
            }
        },
        /**
         * Fetches all games of the site
         */
        async fetchGames() {
            if (this.games.length === 0) {
                this.games = await useGet('/games');
            }
        },
        async nuxtServerInit() {
            try {
                this.user = await useGet('/user');
            } catch (error) {
                console.log("ERR");
                console.log(error.req);
                console.error(error.message);
            }
        },
        setUserAvatar(avatar) {
            this.user.avatar = avatar;
        }
    }
});