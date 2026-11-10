// import { h } from 'vue';
import { createRouter, createWebHistory, RouterView } from 'vue-router';

/**
 * Authentication
 * 
 */
// const Login = () => import('@/views/authentication/Login.vue');

const Dashboard = () => import('@/views/Dashboard.vue');

/**
 * Permissions
 *
 */
// const PermissionsIndex  = () => import('@/views/permissions/Index.vue');
// const PermissionsCreate = () => import('@/views/permissions/Create.vue');
// const PermissionsView   = () => import('@/views/permissions/View.vue');
// const PermissionsEdit   = () => import('@/views/permissions/Edit.vue');

/**
 * Roles
 *
 */
// const RolesIndex             = () => import('@/views/roles/Index.vue');
// const RolesCreate            = () => import('@/views/roles/Create.vue');
// const RolesView              = () => import('@/views/roles/View.vue');
// const RolesEdit              = () => import('@/views/roles/Edit.vue');
// const RolesAssignPermissions = () => import('@/views/roles/AssignPermissions.vue');

/**
 * Users
 * 
 */
const UsersIndex       = () => import('@/views/users-new/Index.vue');
// const UsersCreate      = () => import('@/views/users/Create.vue');
// const UsersView        = () => import('@/views/users/View.vue');
// const UsersEdit        = () => import('@/views/users/Edit.vue');
// const UsersAssignRoles = () => import('@/views/users/AssignRoles.vue');

const router = createRouter({
    history: createWebHistory(),
    routes: [
        // Home
        { path: '/', name: 'home', component: Dashboard },
        // { path: '/dashboard', name: 'dashboard', component: Dashboard },

        // Permissions
        // { path: '/permissions', name: 'permissions.index', component: PermissionsIndex},
        // { path: '/permissions/create', name: 'permissions.create', component: PermissionsCreate },
        // { path: '/permissions/:id', name: 'permissions.view', component: PermissionsView },
        // { path: '/permissions/:id/edit', name: 'permissions.edit', component: PermissionsEdit },

        // Roles
        // { path: '/roles', name: 'roles.index', component: RolesIndex},
        // { path: '/roles/create', name: 'roles.create', component: RolesCreate },
        // { path: '/roles/:id', name: 'roles.view', component: RolesView },
        // { path: '/roles/:id/edit', name: 'roles.edit', component: RolesEdit },
        // { path: '/roles/:id/assign-permissions', name: 'roles.assign-permissions', component: RolesAssignPermissions },

        // Users
        { path: '/users', name: 'users.index', component: UsersIndex },
        // { path: '/users/create', name: 'users.create', component: UsersCreate },
        // { path: '/users/:id', name: 'users.view', component: UsersView },
        // { path: '/users/:id/edit', name: 'users.edit', component: UsersEdit },
        // { path: '/users/:id/assign-roles', name: 'users.assign-roles', component: UsersAssignRoles },
    ],
    scrollBehavior(to, from, savedPosition) {
        if (to.hash) {
            return { el: to.hash, behavior: 'smooth' };
        }
    }
});

// router.beforeEach((to, from, next) => {
//     if (localStorage.getItem('selectedCorporation') === null) {
//         if (to.name === 'corporations.select') {
//             return next();
//         }

//         return next({
//             path: '/corporations/select'
//         });
//     } else {
//         console.log('found selected corporation');
//         return next();
//    }
// });

export default router;
