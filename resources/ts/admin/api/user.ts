import axios from 'axios';

export interface UserRecord {
    nickname: string;
    username: string;
    avatar: string;
    wallet_account: string;
}

export function users(params) {
    return axios.get<UserRecord[]>('users', {params});
}

export function storeUsers(data) {
    return axios.post<UserRecord>('users', data);
}

export function updateUsers(userId, data) {
    return axios.put<UserRecord>('users/' + userId, data);
}
