// https://nuxt.com/docs/api/configuration/nuxt-config

import IconsResolver from 'unplugin-icons/resolver';
import Icons from 'unplugin-icons/vite';
import Components from 'unplugin-vue-components/vite';

export default defineNuxtConfig({
	devServer: {
		host: '0.0.0.0',
	},

	app: {
		head: {
			script: [
				{
					innerHTML: 'window.nitroAds=window.nitroAds||{createAd:function(){return new Promise(e=>{window.nitroAds.queue.push(["createAd",arguments,e])})},addUserToken:function(){window.nitroAds.queue.push(["addUserToken",arguments])},queue:[]};',
					'data-cfasync': false,
				},
				{
					src: 'https://s.nitropay.com/ads-92.js',
					'data-cfasync': false,
					async: true
				}
			]
		}
	},
 
	umami: {
		ignoreLocalhost: true
	},

	runtimeConfig: {
		public: { 
			apiUrl: '',
			siteUrl: '',
			uploadUrl: '',
			storageUrl: '',
			debug_legacy_images: false,
			hcaptchaSiteKey: '',
			version: '3.5',
			commitHash: ''
		},
		innerApiUrl: ''
	},

	hooks: {
		'pages:extend': (routes) => {
			const userSettings = routes.find(page => page.path == '/user-settings');

			userSettings?.children?.push({ path: "/user-settings/profile", file: '~/pages/user-settings/index.vue' });

			routes.push(...[
				{
					path: "/user/:user/edit",
					file: '~/pages/user-settings.vue',
					children: [
						{ path: "", file: '~/pages/user-settings/index.vue' },
						{ path: "account", file: '~/pages/user-settings/account.vue' },
						{ path: "content", file: '~/pages/user-settings/content.vue' },
						{ path: "profile", file: '~/pages/user-settings/index.vue' },
						{ path: "accounts", file: '~/pages/user-settings/accounts.vue' },
						{ path: "api", file: '~/pages/user-settings/api.vue' },
					]
				},
				{ path: "/g/:game/documents", file: '~/pages/documents.vue' },
				{ path: "/g/:game/document/:document", file: '~/pages/document/[document].vue' },
			]);


			//Kinda disgusting, but other way is making components for each one of them and then pages...
			const mod = routes.find(page => page.path == '/mod/:mod()');
			mod?.children?.push({ path: "/mod/:mod/post/:comment", file: '~~/pages/mod/[mod]/index.vue' });
			
			const thread = routes.find(page => page.path == '/thread/:thread()');
			thread?.children?.push({ path: "/thread/:thread/post/:comment", file: '~~/pages/thread/[thread]/index.vue' });

			const game = routes.find(page => page.path == '/g/:game()');

			if (game && game.children) {
				const gameAdmin = game.children.find(page => page.path == 'admin');
				if (gameAdmin && gameAdmin.children) {
					gameAdmin.children.push(...[
						{ path: 'bans', file: '~/pages/admin/bans/index.vue' },
						{ path: 'bans/:ban', file: '~/pages/admin/bans/[ban].vue' },
						{ path: 'cases', file: '~/pages/admin/cases/index.vue' },
						{ path: 'cases/:case', file: '~/pages/admin/cases/[case].vue' },
						{ path: 'forum-categories/:category', file: '~/pages/admin/forum-categories/[category].vue' },
						{ path: 'forum-categories', file: '~/pages/admin/forum-categories/index.vue' },
						{ path: 'roles/:role', file: '~/pages/admin/roles/[role].vue' },
						{ path: 'roles', file: '~/pages/admin/roles/index.vue' },
						{ path: 'tags/:tag', file: '~/pages/admin/tags/[tag].vue' },
						{ path: 'tags', file: '~/pages/admin/tags/index.vue' },
						{ path: 'suspensions', file: '~/pages/admin/suspensions.vue' },
						{ path: 'reports', file: '~/pages/admin/reports.vue' },
						{ path: 'approvals', file: '~/pages/admin/approvals.vue' },
						{ path: 'mods', file: '~/pages/admin/mods.vue' },
						{ path: 'documents', file: '~/pages/admin/documents/index.vue' },
						{ path: 'documents/:document', file: '~/pages/admin/documents/[document].vue' },
						{ path: 'mod-managers', file: '~/pages/admin/mod-managers/index.vue' },
						{ path: 'mod-managers/:modManager', file: '~/pages/admin/mod-managers/[modManager].vue' },
					]);
				}
			}

		}
	},

	optimization: {
		keyedComposables: [
			{ name: 'useFetchMany', argumentLength: 3 },
			{ name: 'useFetchData', argumentLength: 3 },
			{ name: 'useWatchedFetchMany', argumentLength: 4 },
			{ name: 'useEditResource', argumentLength: 5 },
			{ name: 'useResource', argumentLength: 6 },
		]
	},

	// nitro: {
	// 	compressPublicAssets: true,
	// },
	
	vue: {
		propsDestructure: true
	},

	components: [
		"~/components/ui/controls",
		"~/components/ui/layout",
		"~/components/site",
		"~/components/site/pages",
		"~/components/site/mod",
		"~/components/site/notifications",
	],

	css: [
		"@/assets/css/normalize.css",
		"@/assets/css/base.css",
		"@/assets/css/markdown.css",
		"@/assets/css/github-dark.css",
	],

	vite: {
		build: {
			chunkSizeWarningLimit: 1000
		},
		plugins: [
			Components({
				resolvers: [IconsResolver()],
			}),
			Icons({
				defaultClass: 'icon'
			}),
		],
	},

	// ssr: false,

	i18n: {
		strategy: 'no_prefix',

		detectBrowserLanguage: {
			useCookie: true,
			cookieKey: 'locale',
		},
	
		locales: [
			{ code: 'en', file: 'en.json', name: 'English' },
			{ code: 'cs', file: 'cs.json', name: 'Čeština' },
			{ code: 'de', file: 'de.json', name: 'Deutsch' },
			{ code: 'es', file: 'es.json', name: 'Español' },
			{ code: 'fr', file: 'fr.json', name: 'Français' },
			{ code: 'it', file: 'it.json', name: 'Italiano' },
			{ code: 'pl', file: 'pl.json', name: 'Polski' },
			{ code: 'pt-br', file: 'pt_BR.json', name: 'Português' },
			{ code: 'ru', file: 'ru.json', name: 'Русский' },
			{ code: 'tr', file: 'tr.json', name: 'Türkçe' },
			{ code: 'zh-cn', file: 'zh_Hans.json', name: '中文' },
			{ code: 'ko', file: 'ko.json', name: '한국어' },
			{ code: 'ja', file: 'ja.json', name: '日本語' },
			{ code: 'id', file: 'id.json', name: 'Bahasa Indonesia' },
			// { code: 'owo', file: 'owo.ts', name: 'OwO' },
		],

		lazy: true,
		langDir: 'locales',
		defaultLocale: 'en',
	},

	modules: [
		['@nuxtjs/robots', { configPath: '~/robots.config.ts' }],
		// // 'nuxt-delay-hydration',
		'@pinia/nuxt',
		'@nuxtjs/tailwindcss',
		'@vueuse/nuxt',
		'unplugin-icons/nuxt',
		'@nuxtjs/i18n',
		'nuxt-umami'
	],
});
