import axios from 'axios';
import type {AxiosRequestConfig, AxiosResponse} from 'axios';
import {Message} from '@arco-design/web-vue';
import {useUserStore} from '@admin/store';

if (import.meta.env.VITE_API_BASE_URL) {
    axios.defaults.baseURL = import.meta.env.VITE_ADMIN_API_BASE_URL;
}

axios.interceptors.request.use(
    (config: AxiosRequestConfig) => {
        const userStore = useUserStore();
        const token = userStore.token;
        if (token) {
            if (!config.headers) {
                config.headers = {};
            }
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        // do something
        return Promise.reject(error);
    }
);
// add response interceptors
axios.interceptors.response.use(
    (response: AxiosResponse) => {
        return response.data;
    },
    async (error) => {
        const userStore = useUserStore();
        const response = error.response;
        switch (response.status) {
            case 401:
                await userStore.clearToken();
                window.location.reload();
                break;
            case 400:
                if (!response.data.error_type) {
                    Message.error(response.data.message || '');
                }
                break;
            case 403:
            case 404:
            case 500:
                Message.error(response.data.message || '服务器错误');
                break;
            case 422:
                const errors = response.data.errors;
                const errorKeys = Object.keys(errors);
                console.log(errorKeys)
                if (errorKeys.length > 0) {
                    Message.error(errors[errorKeys[0]][0] || '');
                }
                break;

            default:
                Message.error(response.data.message || '服务器内部错误');
        }

        return Promise.reject(response);
    }
);
