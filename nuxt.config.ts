import { defineNuxtConfig } from 'nuxt';
import { resolve } from 'path';

// https://v3.nuxtjs.org/docs/directory-structure/nuxt.config
export default defineNuxtConfig({
	components: [
		"~/components", "~/components/common", "~/components/form",  "~/components/site", "~/components/layout", "~/components/pages"
	],

	vuestic: {
		css: ['typography']
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
				file: resolve(__dirname, "pages/mod/edit-mod.vue")
			});

			routes.push({
				name: "category",
				path: "/category/:id",
				file: resolve(__dirname, "pages/game.vue")
			});

			routes.push({
				name: "game",
				path: "/game/:id",
				file: resolve(__dirname, "pages/game.vue")
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
				name: "admin-page",
				path: "/admin",
				file: resolve(__dirname, "pages/admin/index.vue"),
				children: [
					{
						name: "admin-page-roles",
						path: "/admin/roles",
						file: resolve(__dirname, "pages/admin/admin-roles.vue")
					},
					{
						name: "admin-page-tags",
						path: "/admin/tags",
						file: resolve(__dirname, "pages/admin/admin-tags.vue")
					},
					{
						name: "admin-page-categories",
						path: "/admin/categories",
						file: resolve(__dirname, "pages/admin/admin-categories.vue")
					},
					{
						name: "admin-page-games",
						path: "/admin/games",
						file: resolve(__dirname, "pages/admin/admin-games.vue")
					},
					{
						name: "admin-page-users",
						path: "/admin/users",
						file: resolve(__dirname, "pages/admin/admin-users.vue")
					},
					{
						name: "admin-page-edit-role",
						path: "/admin/roles/:id",
						file: resolve(__dirname, "pages/admin/admin-edit-role.vue")
					},
					{
						name: "admin-page-edit-category",
						path: "/admin/categories/:id",
						file: resolve(__dirname, "pages/admin/admin-edit-category.vue")
					},
					{
						name: "admin-page-edit-game",
						path: "/admin/games/:id",
						file: resolve(__dirname, "pages/admin/admin-edit-game.vue")
					}
				]
			});
		}
	},

	//This converts these libraries to work with es6 import or something like that
	build: {
		transpile: [
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
