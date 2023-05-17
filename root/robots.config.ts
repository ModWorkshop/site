export default [
    { UserAgent: '*' },
    { Disallow: 'admin/*' },
    { Disallow: 'games/*/admin/*' },
    { Disallow: 'verify-email' },
    { Disallow: 'login-redirect' },
    { Disallow: 'link-redirect' },
    { Disallow: 'user-settings' },
    { Disallow: 'user/*/edit/*' },
    { Disallow: 'mod/*/edit/*' },
    { Disallow: 'thread/*/edit/*' },
    { Disallow: 'me/*' },
    { Disallow: 'notifications' },
    { BlankLine: true },

    // Be aware that this will NOT work on target: 'static' mode
    { Sitemap: (req) => `${process.env.NUXT_PUBLIC_SITE_URL}/sitemap.xml` }
];