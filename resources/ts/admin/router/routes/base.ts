import {REDIRECT_ROUTE_NAME} from '@admin/router/constants';
import {AppRouteRecordRaw} from './types';

export const DEFAULT_LAYOUT = () => import('@admin/layout/default/Index.vue');

export const PAGE_LAYOUT = () => import('@admin/layout/page/Index.vue');

export const REDIRECT_MAIN: AppRouteRecordRaw = {
    path: '/redirect',
    name: 'redirectWrapper',
    component: DEFAULT_LAYOUT,
    meta: {
        requiresAuth: true,
        hideInMenu: true,
    },
    children: [
        {
            path: '/redirect/:path',
            name: REDIRECT_ROUTE_NAME,
            component: () => import('@admin/views/base/redirect/Index.vue'),
            meta: {
                requiresAuth: true,
                hideInMenu: true,
            },
        },
    ],
};

export const NOT_FOUND_ROUTE = {
    path: '/:pathMatch(.*)*',
    name: 'notFound',
    component: () => import('@admin/views/base/not-found/Index.vue'),
};
