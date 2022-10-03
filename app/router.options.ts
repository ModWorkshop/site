import type { RouterOptions } from '@nuxt/schema';

// https://router.vuejs.org/api/interfaces/routeroptions.html
/**
 * Some routes may not be defined as we do use Nuxt's pages
 * I just personally dislike the way pages with params are defined
 * imo filesystem is not the place to define metadata like that and frnakly it's
 * utterly disgusting to see [id].vue or [id].jsx, why did NextJS pupularize this shit?
 */
export default <RouterOptions> {
    routes: (routes) => [
        ...routes,
        {
            name: "mod",
            path: "/mod/:modId",
            component: () => import('~/pages/mod.vue'),
            children: [
                {
                    name: "view-mod",
                    path: "",
                    component: () => import('~/pages/mod/view.vue')
                },
                {
                    name: "mod-comment",
                    path: "post/:commentId",
                    component: () => import('~/pages/mod/view.vue')
                },
                {
                    name: "download-file",
                    path: "download/:fileId",
                    component: () => import('~/pages/mod/download-file.vue')
                },
            ]
        },
        {
            name: "edit-mod",
            path: "/mod/:modId/edit",
            component: () => import('~/pages/mod/edit-mod.vue')
        },
        {
            name: "game",
            path: "/g/:gameId",
            component: () => import('~/pages/game.vue')
        },
        {
            name: "user",
            path: "/user/:userId",
            component: () => import('~/pages/user.vue')
        },
        {
            name: "specific-user-settings",
            path: "/user/:userId/edit",
            component: () => import('~/pages/user-settings.vue'),
            children: [
                {
                    name: "edit-user",
                    path: "",
                    component: () => import('~/pages/user-settings/index.vue')
                },
                {
                    name: "edit-user-content",
                    path: "content",
                    component: () => import('~/pages/user-settings/content.vue')
                },
                {
                    name: "edit-user-games",
                    path: "games",
                    component: () => import('~/pages/user-settings/games.vue')
                },
                {
                    name: "edit-user-profile",
                    path: "profile",
                    component: () => import('~/pages/user-settings/profile.vue')
                },
            ]
        },
        {
            name: "user-at",
            path: "/@:userId",
            component: () => import('~/pages/user.vue')
        },
        {
            name: "edit-thread",
            path: "/forum/post",
            component: () => import('~/pages/edit-thread.vue')
        },
        {
            name: "game-upload",
            path: "/g/:gameId/upload",
            component: () => import('~/pages/upload.vue')
        },
        {
            name: "game-forum",
            path: "/g/:gameId/forum",
            component: () => import('~/pages/forum.vue')
        },
        {
            name: "game-post-thread",
            path: "/g/:gameId/forum/post",
            component: () => import('~/pages/edit-thread.vue')
        },
        {
            name: "game-edit-thread",
            path: "/thread/:threadId/edit",
            component: () => import('~/pages/edit-thread.vue')
        },
        {
            name: "thread",
            path: "/thread/:threadId",
            component: () => import('~/pages/thread.vue')
        },
        {
            name: "thread-reply",
            path: "/thread/:threadId/post/:commentId",
            component: () => import('~/pages/thread.vue')
        },
        {
            name: "document",
            path: "/docs/:documentId",
            component: () => import('~/pages/document.vue')
        },
        {
            name: "game-documents",
            path: "/g/:gameId/docs",
            component: () => import('~/pages/docs.vue')
        },
        {
            name: "game-document",
            path: "/g/:gameId/docs/:documentId",
            component: () => import('~/pages/document.vue')
        },
        {
            name: "admin-new-game",
            path: "/admin/games/new",
            component: () => import('~/pages/admin/game.vue'),
            children: [
                {
                    name: "new-game-settings",
                    path: "",
                    component: () => import('~/pages/admin/edit-game.vue')
                },
            ]
        },
        {
            name: "admin-game",
            path: "/admin/games/:gameId",
            component: () => import('~/pages/admin/game.vue'),
            children: [
                {
                    name: "admin-game-home",
                    path: "",
                    component: () => import('~/pages/admin/game-home.vue')
                },
                {
                    name: "admin-edit-game",
                    path: "settings",
                    component: () => import('~/pages/admin/edit-game.vue')
                },
                {
                    name: "admin-game-tags",
                    path: "tags",
                    component: () => import('~/pages/admin/tags.vue')
                },
                {
                    name: "admin-game-forum-categories",
                    path: "forum-categories",
                    component: () => import('~/pages/admin/forum-categories.vue')
                },
                {
                    name: "admin-game-edit-forum-category",
                    path: "forum-categories/:categoryId",
                    component: () => import('~/pages/admin/edit-forum-category.vue')
                },
                {
                    name: "admin-game-categories",
                    path: "categories",
                    component: () => import('~/pages/admin/categories.vue')
                },
                {
                    name: "admin-game-mods",
                    path: "mods",
                    component: () => import('~/pages/admin/mods.vue')
                },
                {
                    name: "admin-game-edit-category",
                    path: "categories/:categoryId",
                    component: () => import('~/pages/admin/edit-category.vue')
                },
                {
                    name: "admin-game-edit-tag",
                    path: "tags/:tagId",
                    component: () => import('~/pages/admin/edit-tag.vue')
                },
                {
                    name: "admin-game-docs",
                    path: "docs",
                    component: () => import('~/pages/admin/docs.vue')
                },
                {
                    name: "admin-game-reports",
                    path: "reports",
                    component: () => import('~/pages/admin/reports.vue')
                },
                {
                    name: "admin-game-edit-document",
                    path: "docs/:documentId",
                    component: () => import('~/pages/admin/edit-document.vue')
                },
                {
                    name: "admin-game-suspensions",
                    path: "suspensions",
                    component: () => import('~/pages/admin/suspensions.vue')
                },
                {
                    name: "admin-game-approvals",
                    path: "approvals",
                    component: () => import('~/pages/admin/approvals.vue')
                },
                {
                    name: "admin-game-templates",
                    path: "instructions-templates",
                    component: () => import('~/pages/admin/instructs-templates.vue')
                },
                {
                    name: "admin-game-template",
                    path: "instructions-templates/:templateId",
                    component: () => import('~/pages/admin/edit-instructs-template.vue')
                },
                {
                    name: "admin-game-roles",
                    path: "roles",
                    component: () => import('~/pages/admin/roles.vue')
                },
                {
                    name: "admin-game-edit-role",
                    path: "roles/:roleId",
                    component: () => import('~/pages/admin/edit-role.vue')
                },
                {
                    name: "admin-game-bans",
                    path: "bans",
                    component: () => import('~/pages/admin/bans.vue')
                },
                {
                    name: "admin-game-cases",
                    path: "cases",
                    component: () => import('~/pages/admin/cases.vue')
                },
                {
                    name: "admin-game-edit-case",
                    path: "cases/:caseId",
                    component: () => import('~/pages/admin/edit-case.vue')
                },
            ]
        },
        {
            name: "admin-page",
            path: "/admin",
            component: () => import('~/pages/admin.vue'),
            children: [
                {
                    name: "admin-settings",
                    path: "settings",
                    component: () => import('~/pages/admin/settings.vue')
                },
                {
                    name: "admin-roles",
                    path: "roles",
                    component: () => import('~/pages/admin/roles.vue')
                },
                {
                    name: "admin-edit-role",
                    path: "roles/:roleId",
                    component: () => import('~/pages/admin/edit-role.vue')
                },
                {
                    name: "admin-tags",
                    path: "tags",
                    component: () => import('~/pages/admin/tags.vue')
                },
                {
                    name: "admin-games",
                    path: "games",
                    component: () => import('~/pages/admin/games.vue')
                },
                {
                    name: "admin-mods",
                    path: "mods",
                    component: () => import('~/pages/admin/mods.vue')
                },
                {
                    name: "admin-forum-categories",
                    path: "forum-categories",
                    component: () => import('~/pages/admin/forum-categories.vue')
                },
                {
                    name: "admin-users",
                    path: "users",
                    component: () => import('~/pages/admin/users.vue')
                },
                {
                    name: "admin-edit-forum-category",
                    path: "forum-categories/:categoryId",
                    component: () => import('~/pages/admin/edit-forum-category.vue')
                },
                {
                    name: "admin-edit-tag",
                    path: "tags/:tagId",
                    component: () => import('~/pages/admin/edit-tag.vue')
                },
                {
                    name: "admin-bans",
                    path: "bans",
                    component: () => import('~/pages/admin/bans.vue')
                },
                {
                    name: "admin-cases",
                    path: "cases",
                    component: () => import('~/pages/admin/cases.vue')
                },
                {
                    name: "admin-edit-case",
                    path: "cases/:caseId",
                    component: () => import('~/pages/admin/edit-case.vue')
                },
                {
                    name: "admin-docs",
                    path: "docs",
                    component: () => import('~/pages/admin/docs.vue')
                },
                {
                    name: "admin-reports",
                    path: "reports",
                    component: () => import('~/pages/admin/reports.vue')
                },
                {
                    name: "admin-suspensions",
                    path: "suspensions",
                    component: () => import('~/pages/admin/suspensions.vue')
                },
                {
                    name: "admin-edit-document",
                    path: "docs/:documentId",
                    component: () => import('~/pages/admin/edit-document.vue')
                },
                {
                    name: "admin-approvals",
                    path: "approvals",
                    component: () => import('~/pages/admin/approvals.vue')
                },
            ]
        }
    ]
};