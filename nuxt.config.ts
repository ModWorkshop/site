import { resolve } from 'path';

// https://v3.nuxtjs.org/docs/directory-structure/nuxt.config

export default defineNuxtConfig({
	runtimeConfig: {
		public: {
			apiUrl: '',
			siteUrl: ''
		}
	},

	components: [
		"~/components", "~/components/common", "~/components/form",  "~/components/site", "~/components/layout", "~/components/pages"
	],

	vuestic: {
		css: ['typography'],
		config: {
			components: {
				VaDropdown: {
					preventOverflow: true,
				}
			}
		}
	},

	css: [
		"@/assets/css/normalize.css",
		"@/assets/css/base.css",
		"@/assets/css/markdown.css",
		"@/assets/css/module-changes.css",
		"@/assets/css/github-dark.css",
	],

	hooks: {
		'imports:dirs' (dirs) {
			dirs.push(resolve(__dirname, 'utils'));
		},
		'pages:extend' (routes) {
			routes.push({
				name: "mod",
				path: "/mod/:modId",
				file: resolve(__dirname, "pages/mod/index.vue"),
				children: [
					{
						name: "view-mod",
						path: "",
						file: resolve(__dirname, "pages/mod/view.vue")
					},
					{
						name: "mod-comment",
						path: "post/:commentId",
						file: resolve(__dirname, "pages/mod/view.vue")
					},
					{
						name: "download-file",
						path: "download/:fileId",
						file: resolve(__dirname, "pages/mod/download-file.vue")
					},
				]
			});

			routes.push({
				name: "edit-mod",
				path: "/mod/:modId/edit",
				file: resolve(__dirname, "pages/mod/edit-mod.vue")
			});

			routes.push({
				name: "upload-mod",
				path: "/upload",
				file: resolve(__dirname, "pages/upload.vue")
			});

			routes.push({
				name: "game",
				path: "/g/:gameId",
				file: resolve(__dirname, "pages/game.vue")
			});

			routes.push({
				name: "user",
				path: "/user/:userId",
				file: resolve(__dirname, "pages/user.vue")
			});

			routes.push({
				name: "specific-user-settings",
				path: "/user/:userId/edit",
				file: resolve(__dirname, "pages/user-settings.vue")
			});

			routes.push({
				name: "user-at",
				path: "/@:userId",
				file: resolve(__dirname, "pages/user.vue")
			});

			routes.push({
				name: "api",
				path: "/api",
				file: resolve(__dirname, "pages/api.vue")
			});

			routes.push({
				name: "forum",
				path: "/forum",
				file: resolve(__dirname, "pages/forum.vue")
			});

			routes.push({
				name: "about",
				path: "/about",
				file: resolve(__dirname, "pages/about.vue")
			});

			routes.push({
				name: "notifications",
				path: "/notifications",
				file: resolve(__dirname, "pages/notifications.vue")
			});

			routes.push({
				name: "edit-thread",
				path: "/forum/post",
				file: resolve(__dirname, "pages/edit-thread.vue")
			});
			
			routes.push({
				name: "game-forum",
				path: "/g/:gameId/forum",
				file: resolve(__dirname, "pages/forum.vue")
			});

			routes.push({
				name: "game-post-thread",
				path: "/g/:gameId/forum/post",
				file: resolve(__dirname, "pages/edit-thread.vue")
			});

			routes.push({
				name: "game-edit-thread",
				path: "/thread/:threadId/edit",
				file: resolve(__dirname, "pages/edit-thread.vue")
			});

			routes.push({
				name: "thread",
				path: "/thread/:threadId",
				file: resolve(__dirname, "pages/thread.vue")
			});

			routes.push({
				name: "thread-reply",
				path: "/thread/:threadId/post/:commentId",
				file: resolve(__dirname, "pages/thread.vue")
			});

			routes.push({
				name: "document",
				path: "/docs/:documentId",
				file: resolve(__dirname, "pages/document.vue")
			});

			routes.push({
				name: "documents",
				path: "/docs",
				file: resolve(__dirname, "pages/docs.vue")
			});

			routes.push({
				name: "game-documents",
				path: "/g/:gameId/docs",
				file: resolve(__dirname, "pages/docs.vue")
			});

			routes.push({
				name: "game-document",
				path: "/g/:gameId/docs/:documentId",
				file: resolve(__dirname, "pages/document.vue")
			});

			routes.push({
				name: "search",
				path: "/search",
				file: resolve(__dirname, "pages/search/index.vue"),
				children: [
					{
						name: "search-mods",
						path: "mods",
						file: resolve(__dirname, "pages/search/mods.vue")
					},
					{
						name: "search-mods",
						path: "mods",
						file: resolve(__dirname, "pages/search/mods.vue")
					},
					{
						name: "search-threads",
						path: "threads",
						file: resolve(__dirname, "pages/search/threads.vue")
					},
					{
						name: "search-users",
						path: "users",
						file: resolve(__dirname, "pages/search/users.vue")
					},
				]
			});

			routes.push({
				name: "admin-game",
				path: "/admin/games/:gameId",
				file: resolve(__dirname, "pages/admin/game.vue"),
				children: [
					{
						name: "admin-edit-game",
						path: "",
						file: resolve(__dirname, "pages/admin/edit-game.vue")
					},
					{
						name: "admin-game-tags",
						path: "tags",
						file: resolve(__dirname, "pages/admin/tags.vue")
					},
					{
						name: "admin-game-forum-categories",
						path: "forum-categories",
						file: resolve(__dirname, "pages/admin/forum-categories.vue")
					},
					{
						name: "admin-game-edit-forum-category",
						path: "forum-categories/:categoryId",
						file: resolve(__dirname, "pages/admin/edit-forum-category.vue")
					},
					{
						name: "admin-game-categories",
						path: "categories",
						file: resolve(__dirname, "pages/admin/categories.vue")
					},
					{
						name: "admin-game-mods",
						path: "mods",
						file: resolve(__dirname, "pages/admin/mods.vue")
					},
					{
						name: "admin-game-edit-category",
						path: "categories/:categoryId",
						file: resolve(__dirname, "pages/admin/edit-category.vue")
					},
					{
						name: "admin-game-edit-tag",
						path: "tags/:tagId",
						file: resolve(__dirname, "pages/admin/edit-tag.vue")
					},
					{
						name: "admin-game-docs",
						path: "docs",
						file: resolve(__dirname, "pages/admin/docs.vue")
					},
					{
						name: "admin-game-reports",
						path: "reports",
						file: resolve(__dirname, "pages/admin/reports.vue")
					},
					{
						name: "admin-game-edit-document",
						path: "docs/:documentId",
						file: resolve(__dirname, "pages/admin/edit-document.vue")
					},
					{
						name: "admin-game-suspensions",
						path: "suspensions",
						file: resolve(__dirname, "pages/admin/suspensions.vue")
					},
					{
						name: "admin-game-approvals",
						path: "approvals",
						file: resolve(__dirname, "pages/admin/approvals.vue")
					},
					{
						name: "admin-game-templates",
						path: "instructions-templates",
						file: resolve(__dirname, "pages/admin/instructs-templates.vue")
					},
					{
						name: "admin-game-template",
						path: "instructions-templates/:templateId",
						file: resolve(__dirname, "pages/admin/edit-instructs-template.vue")
					},
				]
			});

			routes.push({
				name: "admin-page",
				path: "/admin",
				file: resolve(__dirname, "pages/admin/index.vue"),
				children: [
					{
						name: "admin-settings",
						path: "settings",
						file: resolve(__dirname, "pages/admin/settings.vue")
					},
					{
						name: "admin-roles",
						path: "roles",
						file: resolve(__dirname, "pages/admin/roles.vue")
					},
					{
						name: "admin-roles",
						path: "roles",
						file: resolve(__dirname, "pages/admin/roles.vue")
					},
					{
						name: "admin-tags",
						path: "tags",
						file: resolve(__dirname, "pages/admin/tags.vue")
					},
					{
						name: "admin-games",
						path: "games",
						file: resolve(__dirname, "pages/admin/games.vue")
					},
					{
						name: "admin-mods",
						path: "mods",
						file: resolve(__dirname, "pages/admin/mods.vue")
					},
					{
						name: "admin-forum-categories",
						path: "forum-categories",
						file: resolve(__dirname, "pages/admin/forum-categories.vue")
					},
					{
						name: "admin-users",
						path: "users",
						file: resolve(__dirname, "pages/admin/users.vue")
					},
					{
						name: "admin-edit-role",
						path: "roles/:roleId",
						file: resolve(__dirname, "pages/admin/edit-role.vue")
					},
					{
						name: "admin-edit-forum-category",
						path: "forum-categories/:categoryId",
						file: resolve(__dirname, "pages/admin/edit-forum-category.vue")
					},
					{
						name: "admin-edit-tag",
						path: "tags/:tagId",
						file: resolve(__dirname, "pages/admin/edit-tag.vue")
					},
					{
						name: "admin-bans",
						path: "bans",
						file: resolve(__dirname, "pages/admin/bans.vue")
					},
					{
						name: "admin-cases",
						path: "cases",
						file: resolve(__dirname, "pages/admin/cases.vue")
					},
					{
						name: "admin-edit-case",
						path: "cases/:caseId",
						file: resolve(__dirname, "pages/admin/edit-case.vue")
					},
					{
						name: "admin-docs",
						path: "docs",
						file: resolve(__dirname, "pages/admin/docs.vue")
					},
					{
						name: "admin-reports",
						path: "reports",
						file: resolve(__dirname, "pages/admin/reports.vue")
					},
					{
						name: "admin-suspensions",
						path: "suspensions",
						file: resolve(__dirname, "pages/admin/suspensions.vue")
					},
					{
						name: "admin-edit-document",
						path: "docs/:documentId",
						file: resolve(__dirname, "pages/admin/edit-document.vue")
					},
					{
						name: "admin-approvals",
						path: "approvals",
						file: resolve(__dirname, "pages/admin/approvals.vue")
					},
				]
			});
		}
	},

	//This converts these libraries to work with es6 import or something like that
	build: {
		transpile: process.env.NODE_ENV === 'production' ? [
			'markdown-it',
			'markdown-it/lib/common/utils.js',
			'@fortawesome/vue-fontawesome',
			'@fortawesome/fontawesome-svg-core',
			'@fortawesome/free-solid-svg-icons',
			'@fortawesome/free-brands-svg-icons'
		] : [
			'@fortawesome/vue-fontawesome',
			'@fortawesome/fontawesome-svg-core',
			'@fortawesome/free-solid-svg-icons',
			'@fortawesome/free-brands-svg-icons'	
		]
	},

	vite: {
		server: {
			watch: {
				usePolling: true,
			},
		}
	},

	modules: [
		'@pinia/nuxt',
		'nuxt-windicss',
		'@vuestic/nuxt',
		'@vueuse/nuxt'
	],
});
