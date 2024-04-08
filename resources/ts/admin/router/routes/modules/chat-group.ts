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
            path: 'user',
            name: 'chat-group.user',
            component: () => import('@admin/views/chat-group/user/Index.vue'),
            meta: {
                title: '群组成员',
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
