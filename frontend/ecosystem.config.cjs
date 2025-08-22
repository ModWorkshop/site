module.exports = {
	apps: [
		{
			name: 'mws',
			port: '3000',
			exec_mode: 'cluster',
			max_memory_restart: '1G',
			instances: 'max',
			script: './.output/server/index.mjs'
		}
	]
};
