module.exports = {
    apps: [
        {
            name: 'mws',
            port: '3000',
            exec_mode: 'fork',
            max_memory_restart: "1G",
            instances: 4,
            script: './.output/server/index.mjs'
        }
    ]
}