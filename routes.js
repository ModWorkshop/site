export default [
    {
        name: 'index',
        path: '/',
        component: 'pages/index.vue'
    },
    {
        name: "mod",
        path: "/mod/:id",
        component: "pages/mod.vue"
    },
    {
        name: "user",
        path: "/user/:id",
        component: "pages/user.vue"
    },
    {
        name: "edit-mod",
        path: "/mod/:id/edit",
        component: "pages/edit-mod.vue"
    },
    {
        name: "upload-mod",
        path: "/upload",
        component: "pages/edit-mod.vue"
    },
    {
        name: "create-category",
        path: "/create-category",
        component: "pages/edit-category.vue"
    },
    {
        name: "edit-category",
        path: "/category/:id/edit",
        component: "pages/edit-category.vue"
    },
    {
        name: "admin-page",
        path: "/admin/home",
        component: "pages/admin-page.vue"
    },
    {
        name: "admin-page-roles",
        path: "/admin/roles",
        component: "pages/admin-page-roles.vue"
    },
    {
        name: "admin-page-edit-role",
        path: "/admin/roles/:role",
        component: "pages/admin-page-edit-role.vue"
    },
    {
        name: 'login',
        path: '/login',
        component: 'pages/login.vue'
    },
    {
        name: 'register',
        path: '/register',
        component: 'pages/register.vue'
    },
    {
        name: 'user-settings',
        path: '/user-settings',
        component: 'pages/user-settings.vue'
    }
];