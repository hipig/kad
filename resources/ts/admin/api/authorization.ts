import axios from 'axios';
import {UserInfoData} from "./user";

export interface LoginData {
    username: string;
    password: string;
    code: number;
}

export interface LoginRes {
    access_token: string;
    token_type: string;
    expires_in: string;
}

export interface UserRecord {
    name: string;
    username: string;
}

export function authorizations(data: LoginData) {
    return axios.post<LoginRes>('authorizations', data);
}

export function me() {
    return axios.get<UserRecord>('me');
}

export function logout() {
    return axios.delete('authorizations');
}
