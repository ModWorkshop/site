import { User, Game, Tag, Notification, Settings } from './../types/models';
import { defineStore } from 'pinia';
import { Paginator } from '../types/paginator';

interface MainStore {
    user?: User,
    userIsLoading: boolean,
    notifications: Paginator<Notification>,
    notificationCount: number,
    games: Paginator<Game>,
    tags: Paginator<Tag>,
    settings: Settings,
}

let lastTimeout;

export const useStore = defineStore('main', {
    state: (): MainStore => ({
        notifications: null,
        notificationCount: null,
        userIsLoading: false,
        settings: null,
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
        },
        isBanned(state) {
            return !!state.user?.last_ban;
        }
    },
    actions: {
        /**
         * Attempts to login the user (automatically)
         */
        async attemptLoginUser(redirect: string|boolean='/') {
            console.log('Attempting to fetch user data');

            this.userIsLoading = true;

            //https://github.com/nuxt/framework/discussions/5655
            //https://github.com/nuxt/framework/issues/6475
            const [ userData, settings ] = await Promise.all([
                useGet<User>('user'),
                useGet<Settings>('settings'),
                reloadToken()
            ]);

            this.user = userData;
            this.userIsLoading = false;

            this.settings = settings;

            console.log('Trying to reload token');
            
            if (typeof(redirect) == 'string') {
                console.log('Trying to use router');
                const router = useRouter();
                router.push(redirect);
            }
        },

        async logout(redirect: string|boolean='/') {
            await usePost('/logout');
            reloadToken();

            if (typeof(redirect) == 'string') {
                const router = useRouter();
                router.push(redirect);
            }

            this.user = null;
        },

        async getNotifications(page: 1, limit = 40) {
            this.notifications = await useGetMany<Notification>('/notifications', { params: { page, limit } });
        },

        async getNotificationCount() {
            if (this.user) {
                this.notificationCount = await useGet<number>('/notifications/unseen');
            }

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

        setUserAvatar(avatar: string) {
            this.user.avatar = avatar;
        }
    }
});