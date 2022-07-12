import { User, Section, Tag } from './../types/models';
import { defineStore } from 'pinia';

interface MainStore {
    user?: User,
    games: Section[],
    tags: Tag[],
}

export const useStore = defineStore('main', {
    state: (): MainStore => ({
        games: [],
        tags: [],
        user: null
    }),
    getters: {
        hasPermission(state) {
            const permissions = state.user?.permissions;
            if (!permissions) { //This is cached, basically if no permissions, we never have any permissions! Duh.
                return () => false;
            } else {
                return (perm: string) => permissions[perm] === true;
            }
        }
    },
    actions: {
        /**
         * Attempts to login the user (automatically)
         */
        async attemptLoginUser() {
            const userData = await useGet<User>('/user');
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
                const tags = await useGetMany('/tags');
                this.tags = tags.data;
            }
        },
        /**
         * Fetches all games of the site
         */
        async fetchGames() {
            if (this.games.length === 0) {
                const games = await useGetMany('/games');
                this.games = games.data;
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
        setUserAvatar(avatar: string) {
            this.user.avatar = avatar;
        }
    }
});