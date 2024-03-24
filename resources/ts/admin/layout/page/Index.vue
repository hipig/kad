<template>
    <router-view v-slot="{ Component, route }">
        <transition appear>
            <keep-alive v-if="!route.meta?.ignoreCache" :include="cacheList">
                <component :is="Component" :key="route.fullPath"/>
            </keep-alive>
            <component v-else :is="Component" :key="route.fullPath"/>
        </transition>
    </router-view>
</template>

<script lang="ts" setup>
import {computed} from 'vue';
import {useTabBarStore} from '@admin/store';

const tabBarStore = useTabBarStore();

const cacheList = computed(() => tabBarStore.getCacheList);
</script>

