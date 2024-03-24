import {DEFAULT_LAYOUT} from '../base';
import {AppRouteRecordRaw} from '../types';

const WORKPLACE: AppRouteRecordRaw = {
    path: '/workplace',
    name: 'workplace',
    redirect: '/workplace/dashboard',
    component: DEFAULT_LAYOUT,
    meta: {
        title: '仪表盘',
    },
    children: [
        {
            path: 'dashboard',
            name: 'workplace.dashboard',
            component: () => import('@admin/views/workplace/dashboard/Index.vue'),
            meta: {
                title: '概览',
            },
        }
    ],
};

export default WORKPLACE;
