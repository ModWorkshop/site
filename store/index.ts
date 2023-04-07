import { User, Game, Tag, Notification, Settings, Thread } from './../types/models';
import { defineStore } from 'pinia';
import { Paginator } from '../types/paginator';
import { CookieRef } from '#app';
import { longExpiration, reloadToken } from '~~/utils/helpers';

interface MainStore {
    user: User|null,
    notifications: Paginator<Notification>|null,
    notificationCount: number|null,
    reportsCount: number|null,
    waitingCount: number|null,
    currentGame: Game|null,
    savedTheme: CookieRef<string>|null,
    announcements: Thread[],
    colorScheme: string,
    games: Paginator<Game>|null,
    tags: Paginator<Tag>|null,
    settings: Settings|null,
}

let lastTimeout;

export const useStore = defineStore('main', {
    state: (): MainStore => ({
        notifications: null,
        notificationCount: null,
        reportsCount: null,
        waitingCount: null,
        savedTheme: useCookie('theme'),
        colorScheme: useCookie('color-scheme', { expires: longExpiration() }).value ?? 'blue',
        announcements: [],
        currentGame: null,
        settings: null,
        games: null,
        tags: null,
        user: null
    }),
    getters: {
        theme(state) {
            return state.savedTheme === 'light' ? 'light' : 'dark';
        },
        isBanned() {
            return !!this.ban || !!this.gameBan;
        },
        ban(state) {
            return state.user?.ban;
        },
        gameBan(state) {
            return state.currentGame?.user_data?.ban;
        }
    },
    actions: {
        toggleTheme() {
            this.savedTheme = this.theme === 'light' ? null : 'light';
            useCookie('theme', { expires: longExpiration() }).value = this.savedTheme ?? null;
        },
        setGame(game: Game) {
            this.currentGame = game;
        },
        /**
         * Attempts to login the user (automatically)
         */
        async attemptLoginUser(redirect: string|boolean='/') {
            console.log('Attempting to fetch user data');

            //https://github.com/nuxt/framework/discussions/5655
            //https://github.com/nuxt/framework/issues/6475
   
            const [ siteData ] = await Promise.all([
                useGet<{
                    settings: Settings,
                    announcements: Thread[],
                    unseen_notifications: number,
                    reports_count?: number,
                    waiting_count?: number,
                    user?: User
                }>('site-data'),
                reloadToken()
            ]);

            if (siteData.user) {
                this.user = siteData.user;
            } else {
                this.user = null;
            }
            
            this.settings = siteData.settings;
            this.announcements = siteData.announcements;
            this.notificationCount = siteData.unseen_notifications;
            this.reportsCount = siteData.reports_count ?? null;
            this.waitingCount = siteData.waiting_count ?? null;

            console.log('Trying to reload token');

            if (typeof(redirect) == 'string') {
                console.log('Trying to use router');
                const router = useRouter();
                router.push(redirect);
            }
        },

        async reloadUser() {
            this.user = await useGet<User>('user');
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

        async getNotificationCount(first = false) {
            if (this.user && !first) {
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
            this.user!.avatar = avatar;
        },

        hasPermission(perm: string, game?: Game) {
            const permissions = this.user?.permissions;
            if (!this.user) {
                return false;
            } else if (permissions && (permissions.admin === true || permissions[perm] === true)) { //Admins have all permissions
                return true;
            } else if (game && game.user_data && game.user_data.permissions) {
                //Game managers have all *game* permissions. Don't call this on non game permissions.
                if (game.user_data.permissions['manage-game'] === true) {
                    return true;   
                }
                return game.user_data.permissions[perm] === true;
            }
            return false;
        },
    }
});