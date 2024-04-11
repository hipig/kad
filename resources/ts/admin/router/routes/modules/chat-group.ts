import {DEFAULT_LAYOUT} from '../base';
import {AppRouteRecordRaw} from '../types';

const CHAT_GROUP: AppRouteRecordRaw = {
    path: '/chat-group',
    name: 'chat-group.root',
    redirect: '/chat-group/index',
    component: DEFAULT_LAYOUT,
    children: [
        {
            path: 'index',
            name: 'chat-group.index',
            component: () => import('@admin/views/chat-group/list/Index.vue'),
            meta: {
                title: '群组列表',
            },
        },
        {
            path: 'detail',
            name: 'chat-group.detail',
            component: () => import('@admin/views/chat-group/detail/Index.vue'),
            meta: {
                title: '群组详情',
            },
        },
        {
            path: 'message',
            name: 'chat-group.message',
            component: () => import('@admin/views/chat-group/message/Index.vue'),
            meta: {
                title: '群组消息',
            },
        }
    ],
};

export default CHAT_GROUP;
