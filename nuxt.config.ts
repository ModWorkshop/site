import { defineNuxtConfig } from 'nuxt'
import { resolve } from 'path';

// https://v3.nuxtjs.org/docs/directory-structure/nuxt.config
export default defineNuxtConfig({
	components: [
		"~/components", "~/components/common", "~/components/core", "~/components/layout", "~/components/pages"
	],

	css: [
		// '@fortawesome/fontawesome-svg-core/styles.css',
		"@/assets/css/base.css",
		// "@/styles/element.scss",
		"@/assets/css/helpers.css",
		"@/assets/css/github-dark.css",
		"@/assets/css/vuestic-changes.css",
		// "@/assets/css/markdown.css",
	],

	hooks: {
		'pages:extend' (routes) {
			routes.push({
				name: "mod",
				path: "/mod/:id",
				file: resolve(__dirname, "pages/mod.vue")
			});

			routes.push({
				name: "edit-mod",
				path: "/mod/:id/edit",
				file: resolve(__dirname, "pages/edit-mod.vue")
			});
			
			routes.push({
				name: "category",
				path: "/category/:id",
				file: resolve(__dirname, "pages/category.vue")
			});

			routes.push({
				name: "game",
				path: "/game/:id",
				file: resolve(__dirname, "pages/category.vue")
			});

			routes.push({
				name: "user",
				path: "/user/:id",
				file: resolve(__dirname, "pages/user.vue")
			});
		}
	},

	//This converts these libraries to work with es6 import or something like that
	build: {
		transpile: [
		//   'linkify-it',
		//   'uc.micro',
		  'luxon',
		  '@fortawesome/vue-fontawesome',
		  '@fortawesome/fontawesome-svg-core',
		  '@fortawesome/free-solid-svg-icons',
		  '@fortawesome/free-brands-svg-icons',
		]
	},

	
	vite: {
		server: {
			watch: {
				usePolling: true,
			},
		}
	},

	intlify: {
		localeDir: 'locales'
	},

	modules: [
		'@pinia/nuxt',
		'nuxt-windicss',
		'@vuestic/nuxt',
	],
})
