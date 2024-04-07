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

export function chatGroups(params) {
    return axios.get<ChatGroupRecord[]>('chat-groups', {params});
}

export function storeChatGroups(data) {
    return axios.post<ChatGroupRecord>('chat-groups', data);
}

export function dissolveChatGroups(data) {
    return axios.post<ChatGroupRecord[]>('chat-groups/dissolve', data);
}

export function chatGroupUsers(params) {
    return axios.get<ChatGroupUserRecord>('chat-group-users', {params});
}
