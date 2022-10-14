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
		'@vueuse/nuxt',
		'floating-vue/nuxt'
	],
});
