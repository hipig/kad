import {RouteLocationNormalized} from 'vue-router';
import {defineStore} from 'pinia';
import {
    DEFAULT_ROUTE,
    DEFAULT_ROUTE_NAME,
    REDIRECT_ROUTE_NAME,
} from '@admin/router/constants';

export interface TagProps {
    title: string;
    name: string;
    fullPath: string;
    params?: any;
    query?: any;
    ignoreCache?: boolean;
}

export interface TabBarState {
    tagList: TagProps[];
    cacheTabList: Set<string>;
}


const formatTag = (route: RouteLocationNormalized): TagProps => {
    const {name, meta, fullPath, params, query} = route;
    return {
        title: meta?.title,
        name: String(name),
        fullPath,
        params,
        query,
        ignoreCache: meta.ignoreCache,
    };
};

const BAN_LIST = [REDIRECT_ROUTE_NAME];

const useTabBarStore = defineStore('tabBar', {
    state: (): TabBarState => ({
        cacheTabList: new Set([DEFAULT_ROUTE_NAME]),
        tagList: [DEFAULT_ROUTE],
    }),

    getters: {
        getTabList(): TagProps[] {
            return this.tagList;
        },
        getCacheList(): string[] {
            return Array.from(this.cacheTabList);
        },
    },

    actions: {
        updateTabList(route: RouteLocationNormalized) {
            if (BAN_LIST.includes(route.name as string)) return;
            this.tagList.push(formatTag(route));
            if (!route.meta.ignoreCache) {
                this.cacheTabList.add(route.name as string);
            }
        },
        deleteTag(idx: number, tag: TagProps) {
            this.tagList.splice(idx, 1);
            this.cacheTabList.delete(tag.name);
        },
        freshTabList(tags: TagProps[]) {
            this.tagList = tags;
            this.cacheTabList.clear();
            // 要先判断ignoreCache
            this.tagList
                .filter((el) => !el.ignoreCache)
                .map((el) => el.name)
                .forEach((x) => this.cacheTabList.add(x));
        },
        resetTabList() {
            this.tagList = [DEFAULT_ROUTE];
            this.cacheTabList.clear();
            this.cacheTabList.add(DEFAULT_ROUTE_NAME);
        },

        async setTabTitle(title: string, route: RouteLocationNormalized) {
            const findTab = this.getTabList.find((item) => item === route);
            if (findTab) {
                findTab.title = title;
            }
        },
        /**
         * replace tab's path
         * **/
        async updateTabPath(fullPath: string, route: RouteLocationNormalized) {
            const findTab = this.getTabList.find((item) => item === route);
            if (findTab) {
                findTab.fullPath = fullPath;
                findTab.path = fullPath;
            }
        },
    },
});

export default useTabBarStore;
