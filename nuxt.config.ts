// https://v3.nuxtjs.org/docs/directory-structure/nuxt.config

export default defineNuxtConfig({
	runtimeConfig: {
		public: { apiUrl: '', siteUrl: '', storageUrl: '', debug_legacy_images: false }
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

	vite: {
		server: {
			watch: { usePolling: true },
		}
	},

	delayHydration: {
		debug: process.env.NODE_ENV === 'development'
	},

	nuxtIcon: {
		aliases: {
			'': 'fa6-solid:',
		}
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
