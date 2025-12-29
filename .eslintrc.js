module.exports = {
	extends: ['plugin:@wordpress/eslint-plugin/recommended'],
	rules: {
		'camelcase': ['error', { properties: 'always' }],
		'jsx-quotes': ['error', 'prefer-double'],
		'@wordpress/i18n-no-variables': 'error',
		'@wordpress/i18n-hyphenated-range': 'error',
		'@wordpress/no-unsafe-wp-apis': 'error',
		// Turn off prettier formatting errors - use format script instead
		'prettier/prettier': ['warn', {
			singleQuote: true,
			useTabs: true,
			tabWidth: 4,
			printWidth: 100,
			trailingComma: 'es5',
			bracketSpacing: true,
			arrowParens: 'always',
		}],
	},
	plugins: ['@wordpress', 'prettier'],
};
