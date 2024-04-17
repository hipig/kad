import axios from 'axios';

export interface RoleRecord {
    name: string;
    label: string;
}

export function roles(params) {
    return axios.get<RoleRecord[]>('roles', {params});
}

export function storeRoles(data) {
    return axios.post<RoleRecord>('roles', data);
}

export function updateRoles(roleId, data) {
    return axios.put<RoleRecord>('roles/' + roleId, data);
}

export function assignMenuRoles(roleId, data) {
    return axios.post<RoleRecord>('roles/' + roleId + '/assign-menu', data);
}

export function showRoles(roleId) {
    return axios.get<RoleRecord[]>('roles/' + roleId);
}
