import webpack from 'webpack';

export default {
	// Global page headers: https://go.nuxtjs.dev/config-head
	head: {
		title: "ModWorkshop",
		htmlAttrs: {
			lang: "en"
		},
		meta: [
			{
				charset: "utf-8"
			}, {
				name: "viewport",
				content: "width=device-width, initial-scale=1"
			}, {
				hid: "description",
				name: "description",
				content: ""
			}, {
				name: "format-detection",
				content: "telephone=no"
			}
		],
		link: [
			{
				rel: "icon",
				type: "image/x-icon",
				href: "/favicon.ico"
			}
		]
	},

	// Global CSS: https://go.nuxtjs.dev/config-css
	css: [
		'@fortawesome/fontawesome-svg-core/styles.css',
		"element-ui/lib/theme-chalk/index.css",
		"~/assets/css/base",
		"~/assets/css/element-ui-var",
		"~/assets/css/helpers",
		"~/assets/css/github-dark",
		"~/assets/css/markdown",
	],

	// Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
	plugins: [
		"@/plugins/axios.js",
		"@/plugins/element-ui",
		"@/plugins/factory",
		"@/plugins/fa",
		"@/plugins/easy-lightbox",
		{
			mode: 'client',
			src: "@/plugins/vue-observe-visibility"
		}
	],

	// Auto import components: https://go.nuxtjs.dev/config-components
	components: [
		"~/components", "~/components/common", "~/components/core", "~/components/layout", "~/components/pages"
	],

	// Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
	buildModules: [
		"@nuxtjs/color-mode", "@nuxtjs/tailwindcss", '@nuxtjs/composition-api/module', '@nuxtjs/eslint-module'
	],

	scriptSetup: {
		refTransform: true
	},

	// Modules: https://go.nuxtjs.dev/config-modules
	modules: [
		"cookie-universal-nuxt", "@nuxtjs/axios", "@nuxt/image", "@nuxtjs/i18n"
	],

	serverMiddleware: ["~/server-middleware/auth"],

	axios: {
		credentials: true,
		headers: {
			"X-Requested-With": "XMLHttpRequest",
			common: {
				// I don't think there is any situation we'd need to get anything but JSON from Laravel
				Accept: "application/json, text/plain"
			}
		}
	},

	// Build Configuration: https://go.nuxtjs.dev/config-build
	build: {
		transpile: [/^element-ui/],
		plugins: [
			new webpack.NormalModuleReplacementPlugin(/element-ui[/\\]lib[/\\]locale[/\\]lang[/\\]zh-CN/, 'element-ui/lib/locale/lang/en')
		],
		extractCSS: true
	},

	router: {
		extendRoutes(routes) {
			routes.push({ name: "edit-user-page", path: "/user/:id/edit", component: "pages/user-settings.vue" });
			routes.push({ name: "mod-page-proper", path: "/mod/:id", component: "pages/mod.vue" });
			routes.push({ name: "mod-page-proper", path: "/mod/:id", component: "pages/mod.vue" });
			routes.push({ name: "user-page", path: "/user/:id", component: "pages/user.vue" });
			routes.push({ name: "edit-mod-page", path: "/mod/:id/edit", component: "pages/edit-mod.vue" });
			routes.push({ name: "upload-mod-page", path: "/upload", component: "pages/edit-mod.vue" });
			routes.push({ name: "create-category", path: "/create-category", component: "pages/edit-category.vue" });
			routes.push({ name: "fucking-edit-category", path: "/category/:id/edit", component: "pages/edit-category.vue" });
			routes.push({ 
				name: "admin-page-proper",
				path: "/admin/home",
				component: "pages/admin-page.vue"
			});
			routes.push({ 
				name: "admin-page-roles-proper",
				path: "/admin/roles",
				component: "pages/admin-page-roles.vue",
			});
			routes.push({ 
				name: "admin-page-edit-role-proper",
				path: "/admin/roles/:role",
				component: "pages/admin-page-edit-role.vue"
			});
		}
	},

	i18n: {
		locales: [
			{
				code: 'en',
				file: 'en.js'
			}
		],
		strategy: 'no_prefix',
		lazy: true,
		langDir: "lang"
	},

	image: {
		domains: [process.env.API_URL + '/storage']
	}
};
