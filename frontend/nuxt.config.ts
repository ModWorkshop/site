// https://nuxt.com/docs/api/configuration/nuxt-config

import IconsResolver from 'unplugin-icons/resolver';
import Icons from 'unplugin-icons/vite';
import Components from 'unplugin-vue-components/vite';

export default defineNuxtConfig({
	devServer: {
		host: '0.0.0.0'
	},

	features: {
		inlineStyles: true
	},

	site: {
		name: 'ModWorkshop'
	},

	app: {
		head: {
			script: [
				{
					'innerHTML': 'window.nitroAds=window.nitroAds||{createAd:function(){return new Promise(e=>{window.nitroAds.queue.push(["createAd",arguments,e])})},addUserToken:function(){window.nitroAds.queue.push(["addUserToken",arguments])},queue:[]};',
					'data-cfasync': false
				},
				{
					'src': 'https://s.nitropay.com/ads-92.js',
					'data-cfasync': false,
					'async': true
				}
			]
		}
	},

	umami: {
		ignoreLocalhost: true,
		id: 'f37ef034-a1fd-4aa1-bda8-37f830cc8910',
		host: 'https://umami.modworkshop.net/'
	},

	runtimeConfig: {
		public: {
			apiUrl: '',
			siteUrl: '',
			storageUrl: '',
			hcaptchaSiteKey: '',
			version: '3.6.7',
			presignedUpload: false,
			commitHash: ''
		},
		innerApiUrl: ''
	},

	hooks: {
		'pages:extend': routes => {
			const userSettings = routes.find(page => page.path === '/user-settings');

			userSettings?.children?.push({ path: '/user-settings/profile', file: '~/pages/user-settings/index.vue' });

			routes.push(...[
				{
					path: '/user/:user/edit',
					file: '~/pages/user-settings.vue',
					children: [
						{ path: '', file: '~/pages/user-settings/index.vue' },
						{ path: 'account', file: '~/pages/user-settings/account.vue' },
						{ path: 'content', file: '~/pages/user-settings/content.vue' },
						{ path: 'profile', file: '~/pages/user-settings/index.vue' },
						{ path: 'accounts', file: '~/pages/user-settings/accounts.vue' },
						{ path: 'api', file: '~/pages/user-settings/api.vue' }
					]
				},
				{ path: '/g/:game/documents', file: '~/pages/documents.vue' },
				{ path: '/g/:game/document/:document', file: '~/pages/document/[document].vue' }
			]);

			// Kinda disgusting, but other way is making components for each one of them and then pages...
			const mod = routes.find(page => page.path === '/mod/:mod()');
			mod?.children?.push({ path: '/mod/:mod/post/:comment', file: '~/pages/mod/[mod]/index.vue' });

			const thread = routes.find(page => page.path === '/thread/:thread()');
			thread?.children?.push({ path: '/thread/:thread/post/:comment', file: '~/pages/thread/[thread]/index.vue' });

			const game = routes.find(page => page.path === '/g/:game()');

			if (game && game.children) {
				const gameAdmin = game.children.find(page => page.path === 'admin');
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
						{ path: 'audit-log', file: '~/pages/admin/audit-log.vue' }
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
			{ name: 'useResource', argumentLength: 6 }
		]
	},

	// nitro: {
	// 	compressPublicAssets: true,
	// },

	vue: {
		propsDestructure: true
	},

	components: [
		'~/components/ui/controls',
		'~/components/ui/layout',
		'~/components/site',
		'~/components/site/pages',
		'~/components/site/mod',
		'~/components/site/notifications'
	],

	vitalizer: {
		disableStylesheets: 'entry'
	},

	vite: {
		build: {
			chunkSizeWarningLimit: 1000
		},
		plugins: [
			Components({
				resolvers: [IconsResolver()]
			}),
			Icons({
				defaultClass: 'icon'
			})
		],
		resolve: {
			alias: {
				// Fix form-data resolving to browser version instead of Node.js version
				'form-data': 'form-data/lib/form_data'
			}
		},
		ssr: {
			noExternal: ['isomorphic-dompurify']
		}
	},

	// ssr: false,

	i18n: {
		restructureDir: 'app/i18n',
		strategy: 'no_prefix',

		detectBrowserLanguage: {
			useCookie: true,
			cookieKey: 'locale'
		},

		locales: [
			{ code: 'en', language: 'en-US', file: 'en.json', name: 'English' },
			{ code: 'cs', language: 'cs-CZ', file: 'cs.json', name: 'Čeština' },
			{ code: 'de', language: 'de-DE', file: 'de.json', name: 'Deutsch' },
			{ code: 'es', language: 'es', file: 'es.json', name: 'Español' },
			{ code: 'fr', language: 'fr-FR', file: 'fr.json', name: 'Français' },
			{ code: 'it', language: 'it-IT', file: 'it.json', name: 'Italiano' },
			{ code: 'pl', language: 'pl-PL', file: 'pl.json', name: 'Polski' },
			{ code: 'pt-br', language: 'pt-BR', file: 'pt_BR.json', name: 'Português' },
			{ code: 'ru', language: 'ru-RU', file: 'ru.json', name: 'Русский' },
			{ code: 'tr', language: 'tr-TR', file: 'tr.json', name: 'Türkçe' },
			{ code: 'zh-cn', language: 'zh-CN', file: 'zh_Hans.json', name: '中文' },
			{ code: 'ko', language: 'ko-KR', file: 'ko.json', name: '한국어' },
			{ code: 'ja', language: 'ja-JP', file: 'ja.json', name: '日本語' },
			{ code: 'id', language: 'id-ID', file: 'id.json', name: 'Bahasa Indonesia' }
		],

		lazy: true,
		defaultLocale: 'en'
	},

	robots: {
		disallow: [
			'/admin/*',
			'/g/*/admin/*',
			'/game/*/admin/*',
			'/verify-email',
			'/login-redirect',
			'/link-redirect',
			'/user-settings',
			'/user/*/edit/*',
			'/mod/*/edit/*',
			'/thread/*/edit/*',
			'/login',
			'/register',
			'forgot-password',
			'/me/*',
			'/notifications'
		],

		sitemap: '/sitemap.xml'
	},

	modules: [
		'@nuxtjs/robots',
		'@pinia/nuxt',
		'@nuxtjs/tailwindcss',
		'@vueuse/nuxt',
		'unplugin-icons/nuxt',
		'@nuxtjs/i18n',
		'nuxt-umami',
		'@nuxtjs/fontaine',
		'nuxt-easy-lightbox',
		'nuxt-vitalizer',
		'nuxt-seo-utils'
	]
});
