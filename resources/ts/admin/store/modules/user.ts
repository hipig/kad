import {defineStore} from 'pinia';
import {
    authorizations,
    LoginData,
    logout as userLogout,
    me,
} from '@admin/api/authorization';
import {removeRouteListener} from '@admin/utils/route-listener';
import useAppStore from './app';

// @ts-ignore
const useUserStore = defineStore('adminUser', {
    state: () => ({
        token: undefined,
        userInfo: {
            id: undefined,
            name: undefined,
            avatar: undefined,
            email: undefined,
            department_id: undefined,
        }
    }),
    persist: {
        paths: [
            'token'
        ]
    },
    actions: {
        async getUserInfo() {
            this.userInfo = await me();
        },
        // Login
        async login(loginForm: LoginData) {
            try {
                const res = await authorizations(loginForm);
                this.setToken(res.access_token);
            } catch (err) {
                this.clearToken();
                throw err;
            }
        },
        // Logout
        async logout() {
            const appStore = useAppStore();
            try {
                await userLogout();
            } finally {
                this.resetInfo();
                removeRouteListener();
                appStore.clearServerMenu();
            }
        },
        setToken(token: string) {
            this.token = token;
        },
        clearToken() {
            this.setToken(undefined);
        },
        resetInfo() {
            this.$reset();
        }
    }
});

export default useUserStore;
