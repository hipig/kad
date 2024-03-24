import type {Router} from 'vue-router';
import {setRouteEmitter} from '@admin/utils/route-listener';
import setupUserLoginGuard from './userLogin';

function setupPageGuard(router: Router) {
    router.beforeEach(async (to) => {
        // emit route change
        setRouteEmitter(to);
    });
}

export default function createRouteGuard(router: Router) {
    setupPageGuard(router);
    setupUserLoginGuard(router);
}
