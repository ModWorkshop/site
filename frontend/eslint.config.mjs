import stylistic from '@stylistic/eslint-plugin';
import eslint from '@eslint/js';
import eslintPluginVue from 'eslint-plugin-vue';
import globals from 'globals';
import typescriptEslint from 'typescript-eslint';

export default typescriptEslint.config(
	{
		ignores: [
			'.nuxt/**',
			'dist/**',
			'node_modules/**',
			'.output/**',
			'.yarn/**',
			'ecosystem.config.cjs'
		]
	},
	stylistic.configs.customize({
		semi: true,
		commaDangle: 'never',
		braceStyle: '1tbs',
		arrowParens: true
	}),
	{
		extends: [
			eslint.configs.recommended,
			...typescriptEslint.configs.recommended,
			...eslintPluginVue.configs['flat/recommended']
		],
		files: ['**/*.{ts,js,mjs,vue}'],
		languageOptions: {
			ecmaVersion: 'latest',
			sourceType: 'module',
			globals: globals.browser,
			parserOptions: {
				parser: typescriptEslint.parser
			}
		},
		plugins: {
			'@stylistic': stylistic
		},
		rules: {
			// Replaced by the TS one
			'no-unused-vars': 'off',

			'@stylistic/semi': 'error',

			'@typescript-eslint/no-explicit-any': 'warn', // Discourage, rather than block the option
			'@typescript-eslint/no-non-null-assertion': 'off', // While I'd discourage, there are cases where this is totally valid
			'@stylistic/arrow-parens': 'off',

			// Tabs instead of unreadable 2 spaces
			'vue/html-indent': 'off',
			'@stylistic/no-mixed-spaces-and-tabs': ['error', 'smart-tabs'],
			'@stylistic/indent': ['error', 'tab'],
			'@stylistic/no-tabs': 'off',

			'vue/max-attributes-per-line': 'off', // Meh
			'vue/singleline-html-element-content-newline': 'off', // I think it's fine to have it on the same line
			'vue/multi-word-component-names': 'off', // Doesn't get it right for nuxt pages and more
			'vue/html-closing-bracket-spacing': 'off', // Don't reallt get it, what's the point? It looks ugly
			'vue/no-mutating-props': 'off', // Used a lot and works fine for the most part.
			'vue/require-default-prop': 'off', // If it's not set, it can be undefined, what's the problem?
			'vue/first-attribute-linebreak': 'off' // Not that important
		}
	}
);
