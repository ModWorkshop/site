// https://v3.nuxtjs.org/docs/directory-structure/nuxt.config

export default defineNuxtConfig({
	runtimeConfig: {
		public: { apiUrl: '', siteUrl: '', storageUrl: '', debug_legacy_images: false , is_production: false },
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
				{ path: "/mod/:mod/post/:commentId", file: '~/pages/mod/[mod]/index.vue' },
				{ path: "/forum/post", file: '~~/pages/thread/[thread]/edit.vue' },
				{ path: "/g/:game/upload", file: '~/pages/upload.vue' },
				{ path: "/g/:game/forum", file: '~/pages/forum.vue' },
				{ path: "/g/:game/forum/post", file: '~~/pages/thread/[thread]/edit.vue' },
				{ path: "/thread/:thread/post/:comment", file: '~~/pages/thread/[thread]/index.vue' },
				{ path: "/g/:game/documents", file: '~/pages/documents.vue' },
				{ path: "/g/:game/document/:documentId", file: '~/pages/document/[document].vue' },
			]);


			//Kinda disgusting, but other way is making components for each one of them and then pages...
			const game = routes.find(page => page.path == '/game/:game/admin');
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
					{ path: 'documents/:document.', file: '~/pages/admin/documents/[document].vue' },
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

	delayHydration: {
		mode: 'init'
	},

	nitro: {
		compressPublicAssets: true,
	},
	
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

	//This converts these libraries to work with es6 import or something like that
	build: {
		transpile: process.env.NODE_ENV === 'production' ? [
			'markdown-it',
			'markdown-it/lib/common/utils.js',
		] : [ ]
	},

	modules: [
		'nuxt-delay-hydration',
		'@pinia/nuxt',
		'@nuxtjs/tailwindcss',
		'@vueuse/nuxt',
		'nuxt-icon'
		// 'floating-vue/nuxt'
	],
});
