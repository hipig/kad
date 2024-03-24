import axios from 'axios';

export interface UserRecord {
    nickname: string;
    username: string;
    avatar: string;
    wallet_account: string;
}

export function users() {
    return axios.get<UserRecord[]>('users');
}
