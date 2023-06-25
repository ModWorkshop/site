// https://v3.nuxtjs.org/docs/directory-structure/nuxt.config

import { ProcessVersionsNodePlugin } from "./rollup-plugins/process-versions-node";

export default defineNuxtConfig({
	devServer: {
		host: '0.0.0.0',
	},

	runtimeConfig: {
		public: { apiUrl: '', siteUrl: '', uploadUrl: '', storageUrl: '', debug_legacy_images: false , is_production: false },
		innerApiUrl: ''
	},

	hooks: {
		'pages:extend': (routes) => {
			routes.push(...[
				{
					path: "/user/:user/edit",
					file: '~/pages/user-settings.vue',
					children: [
						{ path: "", file: '~/pages/user-settings/index.vue' },
						{ path: "content", file: '~/pages/user-settings/content.vue' },
						{ path: "profile", file: '~/pages/user-settings/profile.vue' },
						{ path: "accounts", file: '~/pages/user-settings/accounts.vue' },
						{ path: "api", file: '~/pages/user-settings/api.vue' },
					]
				},
				{ path: "/mod/:mod/post/:comment", file: '~/pages/mod/[mod]/index.vue' },
				{ path: "/forum/post", file: '~~/pages/thread/[thread]/edit.vue' },
				{ path: "/g/:game/upload", file: '~/pages/upload.vue' },
				{ path: "/g/:game/forum", file: '~/pages/forum.vue' },
				{ path: "/g/:game/forum/post", file: '~~/pages/thread/[thread]/edit.vue' },
				{ path: "/thread/:thread/post/:comment", file: '~~/pages/thread/[thread]/index.vue' },
				{ path: "/g/:game/documents", file: '~/pages/documents.vue' },
				{ path: "/g/:game/document/:documentId", file: '~/pages/document/[document].vue' },
			]);


			//Kinda disgusting, but other way is making components for each one of them and then pages...
			const game = routes.find(page => page.path == '/game/:game()/admin');

			if (game && game.children) {
				game.children.push(...[
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
				]);
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

	// delayHydration: {
	// 	mode: 'init'
	// },

	// nitro: {
	// 	compressPublicAssets: true,
	// },
	
	components: [
		"~/components", "~/components/common", "~/components/form",  "~/components/site", "~/components/layout", "~/components/pages"
	],

	css: [
		"@/assets/css/normalize.css",
		"@/assets/css/base.css",
		"@/assets/css/markdown.css",
		"@/assets/css/module-changes.css",
		"@/assets/css/github-dark.css",
	],

	vite: {
		build: {
			chunkSizeWarningLimit: 1000
		},
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
			// { code: 'owo', file: 'owo.ts', name: 'OwO' },
		],

		experimental: {
			jsTsFormatResource: true
		},
		
		lazy: true,
		langDir: 'locales',
		defaultLocale: 'en',
	},

	gtag: {
		id: 'G-EGYBGTBHRV'
	},

	nitro: {
		rollupConfig: {
			// @ts-expect-error ???
			plugins: [ProcessVersionsNodePlugin()],
		},
	},
	
	modules: [
		['@nuxtjs/robots', { configPath: '~/robots.config.ts' }],
		// 'nuxt-delay-hydration',
		'@pinia/nuxt',
		'@nuxtjs/tailwindcss',
		'@vueuse/nuxt',
		'nuxt-icon',
		'nuxt-simple-sitemap',
		'@nuxtjs/i18n',
		'nuxt-gtag'
	],
});
