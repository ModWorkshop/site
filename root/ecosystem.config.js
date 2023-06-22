module.exports = {
    apps: [
        {
            name: 'mws',
            port: '3000',
            exec_mode: 'cluster',
            instances: 4,
            script: './.output/server/index.mjs'
        }
    ]
}