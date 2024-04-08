import axios from 'axios';


export interface ChatMessageRecord {
    body: object;
}

export function chatMessages(params) {
    return axios.get<ChatMessageRecord[]>('chat-messages', {params});
}

export function storeChatMessages(data) {
    return axios.post<ChatMessageRecord>('chat-messages', data);
}

export function withdrawChatMessages(data) {
    return axios.post<ChatMessageRecord>('chat-messages/withdraw', data);
}

