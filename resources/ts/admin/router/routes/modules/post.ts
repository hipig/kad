import {DEFAULT_LAYOUT} from '../base';
import {AppRouteRecordRaw} from '../types';

const POST: AppRouteRecordRaw = {
    path: '/post',
    name: 'post.root',
    redirect: '/post/index',
    component: DEFAULT_LAYOUT,
    children: [
        {
            path: 'index',
            name: 'post.index',
            component: () => import('@admin/views/post/list/Index.vue'),
            meta: {
                title: '动态列表',
            },
        },
        {
            path: 'comment',
            name: 'post.comment',
            component: () => import('@admin/views/post/comment/Index.vue'),
            meta: {
                title: '评论列表',
            },
        },
        {
            path: 'report',
            name: 'post.report',
            component: () => import('@admin/views/post/report/Index.vue'),
            meta: {
                title: '动态举报',
            },
        }
    ],
};

export default POST;
