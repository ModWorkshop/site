import { User, Game, Tag, Notification } from './../types/models';
import { defineStore } from 'pinia';
import { Paginator } from '../types/paginator';

interface MainStore {
    user?: User,
    userIsLoading: boolean,
    notifications: Paginator<Notification>,
    notificationCount: number,
    games: Paginator<Game>,
    tags: Paginator<Tag>,
}

let lastTimeout;

export const useStore = defineStore('main', {
    state: (): MainStore => ({
        notifications: null,
        notificationCount: null,
        userIsLoading: false,
        games: null,
        tags: null,
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
            try {
                // If for some reason (cough current poor error handling) it doesn't work on SSR
                if (!this.user) {
                    await this.attemptLoginUser(false);
                }
                if (this.user) {
                    await this.getNotificationCount();
                }
            } catch (error) {
                console.log(error);
                this.userIsLoading = false;
            }
        },
        /**
         * Attempts to login the user (automatically)
         */
        async attemptLoginUser(redirect: string|boolean='/') {
            console.log('Attempting to fetch user data');

            this.userIsLoading = true;

            await reloadToken();

            const userData = await useGet<User>('/user');
            this.user = userData;
            this.userIsLoading = false;

            const router = useRouter();
            await reloadToken();

            if (typeof(redirect) == 'string') {
                router.push(redirect);
            }
        },

        async getNotifications(page: 1, limit = 40) {
            this.notifications = await useGetMany<Notification>('/notifications', { params: { page, limit } });
        },

        async getNotificationCount() {
            this.notificationCount = await useGet<number>('/notifications/unseen');
            if (lastTimeout) {
                clearTimeout(lastTimeout);
            }
            if (process.client) { //!!Avoid loooping on server side!!
                lastTimeout = setTimeout(() => this.getNotificationCount(), 300 * 1000);
            }
        },

        /**
         * Fetches all games of the site
         */
        async fetchGames() {
            if (!this.games) {
                this.games = await useGetMany('/games');
            }
        },

        async nuxtServerInit() {
            try {
                await this.attemptLoginUser(false, false);
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