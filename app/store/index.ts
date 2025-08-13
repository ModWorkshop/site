import type { User, Game, Tag, Notification, Settings, Thread } from '~/types/models';
import { defineStore } from 'pinia';
import { Paginator } from '../types/paginator';
import type { CookieRef } from '#app';

interface MainStore {
    user: User|null,
    notifications: Paginator<Notification>|null,
    notificationCount: number|null,
    reportCount: number|null,
    waitingCount: number|null,
    activity: {
        users: number,
        guests: number
    }|null,
    ads: any[],
    currentGame: Game|null,
    savedTheme: CookieRef<string>|null,
    announcements: Thread[],
    colorScheme: string,
    games: Game[]|null,
    tags: Paginator<Tag>|null,
    settings: Settings|null,
}

let lastTimeout;

export const useStore = defineStore('main', {
    state: (): MainStore => ({
        notifications: null,
        notificationCount: null,
        reportCount: null,
        waitingCount: null,
        savedTheme: useCookie('theme'),
        colorScheme: useCookie('color-scheme', { expires: longExpiration() }).value ?? 'blue',
        activity: null,
        announcements: [],
        currentGame: null,
        settings: null,
        games: null,
        tags: null,
        user: null,
        ads: []
    }),
    getters: {
        theme(state) {
            if (state.savedTheme == 'light') {
                return 'light';
            } else if (state.savedTheme == 'system') {
               return 'system';
            } else {
                return 'dark';
            }
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
        setTheme(theme: 'light'|'dark'|'system') {
            if (theme == 'dark') {
                this.savedTheme = null;
            } else {
                this.savedTheme = theme === 'system' ? 'system' : 'light';
            }
            useCookie('theme', { expires: longExpiration() }).value = this.savedTheme ?? null;
        },
        setGame(game?: Game|null) {
            // if (game) {
            //     console.log('Setting game to', game.name);
            // } else {
            //     console.log('Setting to no game');
            // }
            this.currentGame = game || null;
        },
        /**
         * Attempts to login the user (automatically)
         */
        async attemptLoginUser(redirect: string|boolean='/') {
            await this.loadSiteData();

            if (typeof(redirect) == 'string') {
                useRouter().push(redirect);
            }
        },

        async loadSiteData() {
            // console.log('Attempting to fetch user data');

            //https://github.com/nuxt/framework/discussions/5655
            //https://github.com/nuxt/framework/issues/6475
   
            const { $mwsAPI } = useNuxtApp();

            type SiteData = {
                settings: Settings,
                announcements: Thread[],
                unseen_notifications: number,
                report_count?: number,
                waiting_count?: number,
                user?: User,
                activity: { users: number, guests: number },
                games: Game[]
            };

            let siteData;
            try {
                siteData = await $mwsAPI<SiteData>('site-data');
            } catch (error) {
                console.log(error);
            }

           if (import.meta.client) {
               reloadToken(); // Don't block navigation
           }

            if (siteData.user) {
                this.user = siteData.user;
            } else {
                this.user = null;
            }
            
            this.games = siteData.games;
            this.settings = siteData.settings;
            this.announcements = siteData.announcements;
            this.notificationCount = siteData.unseen_notifications;
            this.reportCount = siteData.report_count ?? null;
            this.waitingCount = siteData.waiting_count ?? null;
            this.activity = siteData.activity;
        },

        async reloadUser() {
            const { $mwsAPI } = useNuxtApp();
            this.user = await $mwsAPI<User>('user');
        },

        async logout(redirect: string|boolean='/') {
            const router = useRouter();

            if (typeof(redirect) == 'string') {
                router.push(redirect);
            }

            await postRequest('/logout');
            reloadToken();

            this.user = null;

            router.push({ force: true });
        },

        async getNotifications(page = 1, limit = 40) {
            const { $mwsAPI } = useNuxtApp();
            this.notifications = await $mwsAPI<Paginator<Notification>>('/notifications', { params: { page, limit } });
        },

        async getNotificationCount() {
            const { $mwsAPI } = useNuxtApp();

            this.notificationCount = await $mwsAPI<number>('/notifications/unseen');
        },
        
        // Reloads game data like announcements.
        async getGameData() {
            const gameData = await getRequest<{ announcements: Thread[], waiting_count: number, report_count: number }>(`games/${this.currentGame!.id}/data`);
            this.currentGame!.announcements = gameData.announcements;
            this.currentGame!.waiting_count = gameData.waiting_count;
            this.currentGame!.report_count = gameData.report_count;
        },

        // Essentially reloads the site data so people don't have to refresh the page
        async reloadSiteData(initial = false) {
            if (!initial && this.user) {
                const promises: Promise<any>[] = [];
                if (this.notifications) {
                    promises.push(this.getNotifications());
                }
                if (this.notificationCount != null) {
                    promises.push(this.getNotificationCount());
                }
                if (this.currentGame) {
                    promises.push(this.getGameData());
                }

                await Promise.all(promises)
            }

            if (initial) {
                await this.loadSiteData();
            } else {
                this.prepareToReloadSiteData();
            }
        },

        prepareToReloadSiteData() {
            if (import.meta.client) { //!!Avoid loooping on server side!!
                if (lastTimeout) {
                    clearTimeout(lastTimeout);
                }
                lastTimeout = setTimeout(() => this.reloadSiteData(), 60 * 1000);
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