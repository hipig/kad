import axios from 'axios';

export interface ChatGroupRecord {
    name: string;
    type: string;
    owner: object;
}

export interface ChatGroupUserRecord {
    name: string;
    type: string;
    owner: object;
}

export interface ChatGroupMessageRecord {
    body: object;
}

export function chatGroups(params) {
    return axios.get<ChatGroupRecord[]>('chat-groups', {params});
}

export function chatGroupMessages(params) {
    return axios.get<ChatGroupMessageRecord[]>('chat-group-messages', {params});
}

export function storeChatGroups(data) {
    return axios.post<ChatGroupRecord>('chat-groups', data);
}

export function dissolveChatGroups(data) {
    return axios.post<ChatGroupRecord[]>('chat-groups/dissolve', data);
}

export function joinChatGroups(groupId, data) {
    return axios.post<ChatGroupRecord[]>('chat-groups/' + groupId + '/join', data);
}

export function chatGroupUsers(params) {
    return axios.get<ChatGroupUserRecord>('chat-group-users', {params});
}

export function sendChatGroupMessages(data) {
    return axios.post<ChatGroupMessageRecord[]>('chat-group-messages/send', data);
}

export function recallChatGroupMessages(data) {
    return axios.post<ChatGroupMessageRecord[]>('chat-group-messages/recall', data);
}
