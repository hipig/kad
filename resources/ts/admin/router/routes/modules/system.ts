import {DEFAULT_LAYOUT} from '../base';
import {AppRouteRecordRaw} from '../types';

const SYSTEM: AppRouteRecordRaw = {
    path: '/system',
    name: 'system.root',
    redirect: '/system/user',
    component: DEFAULT_LAYOUT,
    children: [
        {
            path: 'user',
            name: 'system.user',
            component: () => import('@admin/views/system/user/Index.vue'),
            meta: {
                title: '管理员',
            },
        },
        {
            path: 'role',
            name: 'system.role',
            component: () => import('@admin/views/system/role/Index.vue'),
            meta: {
                title: '角色管理',
            },
        }
    ],
};

export default SYSTEM;
