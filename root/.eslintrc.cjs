module.exports = {
	root: true,
	parser: 'vue-eslint-parser',
	parserOptions: {
		parser: "@typescript-eslint/parser",
		sourceType: "module"
	},
	plugins: [
		'@typescript-eslint',
	],
	rules: {
		'no-undef': ['off'],
		semi: ["error", "always"],
		'vue/html-indent': ['off'],
		'vue/max-attributes-per-line': ['off'],
		'vue/singleline-html-element-content-newline': ['off'],
		'vue/first-attribute-linebreak': ['off'],
		'vue/mustache-interpolation-spacing': ['off'],
		'vue/html-closing-bracket-spacing': ['off'],
		'vue/multi-word-component-names': ['off'],
		'vue/require-default-prop': ['off'],
		'vue/no-mutating-props': ['off'],
		'@typescript-eslint/no-explicit-any': 'warn',
		"@typescript-eslint/no-non-null-assertion": ['off'] // Opinionated, if a programmer deems something to be non-nullable, then there is a reason for that.
	},
	extends: [
		'eslint:recommended',
		'plugin:@typescript-eslint/recommended',
		'plugin:vue/vue3-recommended',
	],
};