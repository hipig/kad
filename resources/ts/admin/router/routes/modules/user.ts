import {DEFAULT_LAYOUT} from '../base';
import {AppRouteRecordRaw} from '../types';

const USER: AppRouteRecordRaw = {
    path: '/user',
    name: 'user.root',
    redirect: '/user/index',
    component: DEFAULT_LAYOUT,
    children: [
        {
            path: 'index',
            name: 'user.index',
            component: () => import('@admin/views/user/list/Index.vue'),
            meta: {
                title: '用户列表',
            },
        },
        {
            path: 'report',
            name: 'user.report',
            component: () => import('@admin/views/user/report/Index.vue'),
            meta: {
                title: '用户举报',
            },
        }
    ],
};

export default USER;
