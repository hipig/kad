import axios from 'axios';

export interface MenuRecord {
    name: string;
    icon: string;
    route: string;
}

export function currentMenu() {
    return axios.get<MenuRecord>('menus/current');
}
