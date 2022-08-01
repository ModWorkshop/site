import { User, Game, Tag, Notification } from './../types/models';
import { defineStore } from 'pinia';
import { Paginator } from '../types/paginator';

type NotificationsPaginator = Paginator<Notification> & {
    total_unseen: number;
}

interface MainStore {
    user?: User,
    notifications: NotificationsPaginator,
    games: Game[],
    tags: Tag[],
}

let lastTimeout;

export const useStore = defineStore('main', {
    state: (): MainStore => ({
        notifications: null,
        games: [],
        tags: [],
        user: null
    }),
    getters: {
        hasPermission(state) {
            const permissions = state.user?.permissions;

            if (!permissions) { //This is cached, basically if no permissions, we never have any permissions! Duh.
                return () => false;
            } else if (permissions.admin) {
                return () => true;
            } else {
                return (perm: string) => permissions[perm] === true;
            }
        }
    },
    actions: {
        async init() {
            if (this.user) {
                await this.getNotifications();
            }
        },
        /**
         * Attempts to login the user (automatically)
         */
        async attemptLoginUser() {
            await reloadToken();

            const userData = await useGet<User>('/user');
            this.user = userData;

            const router = useRouter();
            await reloadToken();

            router.push('/');
        },

        async getNotifications() {
            this.notifications = await useGet<NotificationsPaginator>('/notifications');
            if (lastTimeout) {
                clearTimeout(lastTimeout);
            }
            lastTimeout = setTimeout(() => this.getNotifications(), 10 * 1000);
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