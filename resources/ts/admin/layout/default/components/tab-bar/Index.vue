<template>
    <div class="relative bg-[var(--color-bg-2)]">
        <a-affix ref="affixRef" :offset-top="offsetTop">
            <div class="flex pl-5 bg-[var(--color-bg-2)] border-b border-solid border-[var(--color-border)]">
                <div class="h-8 flex-auto overflow-hidden">
                    <div class="py-1 h-12 space-x-2 whitespace-nowrap overflow-x-auto">
                        <tab-item
                            v-for="(tag, index) in tagList"
                            :key="tag.fullPath"
                            :index="index"
                            :item-data="tag"
                        />
                    </div>
                </div>
                <div class="h-[6.25rem] h-8"></div>
            </div>
        </a-affix>
    </div>
</template>

<script lang="ts" setup>
import {ref, computed, watch} from 'vue';
import type {RouteLocationNormalized} from 'vue-router';
import {listenerRouteChange} from '@admin/utils/route-listener';
import {useAppStore, useTabBarStore} from '@admin/store';
import tabItem from './TabItem.vue';

const appStore = useAppStore();
const tabBarStore = useTabBarStore();

const affixRef = ref();
const tagList = computed(() => {
    return tabBarStore.getTabList;
});
const offsetTop = computed(() => {
    return appStore.navbar ? 60 : 0;
});

watch(
    () => appStore.navbar,
    () => {
        affixRef.value.updatePosition();
    }
);
listenerRouteChange((route: RouteLocationNormalized) => {
    if (
        !route.meta.noAffix &&
        !tagList.value.some((tag) => tag.fullPath === route.fullPath)
    ) {
        tabBarStore.updateTabList(route);
    }
}, true);
</script>
