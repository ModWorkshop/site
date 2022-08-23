import { defineNuxtConfig } from 'nuxt';
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
		"@/assets/css/vuestic-changes.css",
		"@/assets/css/github-dark.css",
	],

	hooks: {
		'autoImports:dirs' (dirs) {
			dirs.push(resolve(__dirname, 'utils'));
		},
		'pages:extend' (routes) {
			routes.push({
				name: "mod",
				path: "/mod/:id",
				file: resolve(__dirname, "pages/mod/index.vue")
			});

			routes.push({
				name: "edit-mod",
				path: "/mod/:id/edit",
				file: resolve(__dirname, "pages/mod/edit-mod.vue")
			});

			routes.push({
				name: "upload-mod",
				path: "/upload",
				file: resolve(__dirname, "pages/upload.vue")
			});

			routes.push({
				name: "game",
				path: "/g/:id",
				file: resolve(__dirname, "pages/game.vue")
			});

			routes.push({
				name: "game-mods",
				path: "/g/:id/mods",
				file: resolve(__dirname, "pages/game-mods.vue")
			});

			routes.push({
				name: "user",
				path: "/user/:id",
				file: resolve(__dirname, "pages/user.vue")
			});

			routes.push({
				name: "user-at",
				path: "/@:id",
				file: resolve(__dirname, "pages/user.vue")
			});

			routes.push({
				name: "api",
				path: "/api",
				file: resolve(__dirname, "pages/api.vue")
			});
		
			routes.push({
				name: "blog",
				path: "/blog",
				file: resolve(__dirname, "pages/blog.vue")
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
				path: "/g/:id/forum",
				file: resolve(__dirname, "pages/forum.vue")
			});

			routes.push({
				name: "edit-thread",
				path: "/g/:id/forum/post",
				file: resolve(__dirname, "pages/edit-thread.vue")
			});

			routes.push({
				name: "thread",
				path: "/thread/:id",
				file: resolve(__dirname, "pages/thread.vue")
			});

			routes.push({
				name: "admin-page",
				path: "/admin",
				file: resolve(__dirname, "pages/admin/index.vue"),
				children: [
					{
						name: "admin-roles",
						path: "/admin/roles",
						file: resolve(__dirname, "pages/admin/roles.vue")
					},
					{
						name: "admin-tags",
						path: "/admin/tags",
						file: resolve(__dirname, "pages/admin/tags.vue")
					},
					{
						name: "admin-categories",
						path: "/admin/categories",
						file: resolve(__dirname, "pages/admin/categories.vue")
					},
					{
						name: "admin-games",
						path: "/admin/games",
						file: resolve(__dirname, "pages/admin/games.vue")
					},
					{
						name: "admin-users",
						path: "/admin/users",
						file: resolve(__dirname, "pages/admin/users.vue")
					},
					{
						name: "admin-edit-role",
						path: "/admin/roles/:id",
						file: resolve(__dirname, "pages/admin/edit-role.vue")
					},
					{
						name: "admin-edit-category",
						path: "/admin/categories/:id",
						file: resolve(__dirname, "pages/admin/edit-category.vue")
					},
					{
						name: "admin-forum-categories",
						path: "/admin/forum-categories",
						file: resolve(__dirname, "pages/admin/forum-categories.vue")
					},
					{
						name: "admin-edit-forum-category",
						path: "/admin/forum-categories/:id",
						file: resolve(__dirname, "pages/admin/edit-forum-category.vue")
					},
					{
						name: "admin-edit-game",
						path: "/admin/games/:id",
						file: resolve(__dirname, "pages/admin/edit-game.vue")
					}
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
