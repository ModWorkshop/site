import webpack from 'webpack';
import routes from './routes';

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
		"@/plugins/ftch",
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
		"@nuxtjs/color-mode", 'nuxt-windicss', '@nuxtjs/composition-api/module', ['@pinia/nuxt', { disableVuex: true }]
	],

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
		middleware: ['pinia-init'],
		extendRoutes() {
			return routes;
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
