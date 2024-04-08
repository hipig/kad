import {DEFAULT_LAYOUT} from '../base';
import {AppRouteRecordRaw} from '../types';

const CHAT_GROUP: AppRouteRecordRaw = {
    path: '/chat-message',
    name: 'chat-message.root',
    redirect: '/chat-message/index',
    component: DEFAULT_LAYOUT,
    children: [
        {
            path: 'index',
            name: 'chat-message.index',
            component: () => import('@admin/views/chat/message/Index.vue'),
            meta: {
                title: '单聊消息',
            },
        }
    ],
};

export default CHAT_GROUP;
