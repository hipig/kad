import {defineStore} from 'pinia';
import {Notification} from '@arco-design/web-vue';
import type {RouteRecordNormalized} from 'vue-router';
import {currentMenus, RouteRecord} from '@admin/api/menu';

export interface AppState {
    theme: string;
    colorWeak: boolean;
    navbar: boolean;
    menu: boolean;
    hideMenu: boolean;
    menuCollapse: boolean;
    footer: boolean;
    themeColor: string;
    menuWidth: number;
    globalSettings: boolean;
    device: string;
    tabBar: boolean;
    menuFromServer: boolean;
    serverMenu: RouteRecord[];

    [key: string]: unknown;
}

// @ts-ignore
const useAppStore = defineStore('app', {
    state: (): AppState => ({
            theme: "light",
            colorWeak: false,
            navbar: true,
            menu: true,
            hideMenu: false,
            menuCollapse: false,
            footer: true,
            themeColor: "#165DFF",
            menuWidth: 220,
            device: "desktop",
            tabBar: true,
            menuFromServer: false,
            serverMenu: []
        }
    ),

    getters: {
        appCurrentSetting(state: AppState): AppState {
            return {...state};
        },
        appDevice(state: AppState) {
            return state.device;
        },
        appAsyncMenus(state: AppState): RouteRecordNormalized[] {
            return state.serverMenu as unknown as RouteRecordNormalized[];
        },
    },

    actions: {
        // Update app settings
        updateSettings(partial: Partial<AppState>) {
            // @ts-ignore-next-line
            this.$patch(partial);
        },

        // Change theme color
        toggleTheme(dark: boolean) {
            if (dark) {
                this.theme = 'dark';
                document.body.setAttribute('arco-theme', 'dark');
            } else {
                this.theme = 'light';
                document.body.removeAttribute('arco-theme');
            }
        },
        toggleDevice(device: string) {
            this.device = device;
        },
        toggleMenu(value: boolean) {
            this.hideMenu = value;
        },
        async fetchServerMenuConfig() {
            try {
                this.serverMenu = await currentMenus();
            } catch (e) {
                Notification.error({
                    id: 'menuNotice',
                    content: e.message,
                    closable: true,
                });
            }
        },
        clearServerMenu() {
            this.serverMenu = [];
        },
    },
});

export default useAppStore;
