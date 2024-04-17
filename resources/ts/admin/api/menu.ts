import axios from 'axios';

export interface MenuRecord {
    name: string;
    icon: string;
    route: string;
}

export function treeMenus() {
    return axios.get<MenuRecord>('menus/tree');
}

export function currentMenus() {
    return axios.get<MenuRecord>('menus/current');
}
