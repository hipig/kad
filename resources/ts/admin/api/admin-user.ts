import axios from 'axios';

export interface AdminUserRecord {
    name: string;
    username: string;
    avatar: string;
}

export function users(params) {
    return axios.get<AdminUserRecord[]>('admin-users', {params});
}

export function storeUsers(data) {
    return axios.post<AdminUserRecord>('admin-users', data);
}

export function updateUsers(userId, data) {
    return axios.put<AdminUserRecord>('admin-users/' + userId, data);
}

export function changeStatusUsers(userId) {
    return axios.post<AdminUserRecord>('admin-users/' + userId + '/change-status');
}

export function changeMePassword(data) {
    return axios.post<AdminUserRecord>('admin-users/change-password', data);
}
