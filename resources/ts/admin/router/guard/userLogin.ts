import type {Router, LocationQueryRaw} from 'vue-router';
import NProgress from 'nprogress'; // progress bar

import {useAppStore, useUserStore} from '@admin/store';

export default function setupUserLoginGuard(router: Router) {
    router.beforeEach(async (to, from, next) => {
        NProgress.start();
        const userStore = useUserStore();
        const appStore = useAppStore();

        if (userStore.token) {
            if (!userStore.userInfo.id) {
                await userStore.getUserInfo();
            }
            if (!appStore.appAsyncMenus.length) {
                await appStore.fetchServerMenuConfig();
            }
            next();
        } else {
            if (to.name === 'login') {
                next();
                return;
            }

            next({
                name: 'login',
                query: {
                    redirect: to.name,
                    ...to.query,
                } as LocationQueryRaw,
            });
            return;
        }
    });

    router.afterEach(() => {
        NProgress.done();
    })
}
