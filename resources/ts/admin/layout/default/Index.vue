<template>
    <a-layout class="w-full h-full" :class="{ mobile: appStore.hideMenu }">
        <div v-if="navbar" class="fixed top-0 inset-x-0 h-[3.75rem] z-[100]">
            <NavBar/>
        </div>
        <a-layout>
            <a-layout>
                <a-layout-sider
                    v-if="renderMenu"
                    v-show="!hideMenu"
                    class="fixed top-0 inset-y-0 z-[99]"
                    breakpoint="xl"
                    :collapsed="collapsed"
                    :collapsible="true"
                    :width="menuWidth"
                    :style="{ paddingTop: navbar ? '60px' : '' }"
                    :hide-trigger="true"
                    @collapse="setCollapsed"
                >
                    <div class="h-full overflow-y-auto">
                        <Menu/>
                    </div>
                </a-layout-sider>
                <a-drawer
                    v-if="hideMenu"
                    :visible="drawerVisible"
                    placement="left"
                    :footer="false"
                    mask-closable
                    :closable="false"
                    @cancel="drawerCancel"
                >
                    <Menu/>
                </a-drawer>
                <a-layout class="min-h-screen overflow-y-hidden bg-[var(--color-fill-2)]" :style="paddingStyle">
                    <TabBar v-if="appStore.tabBar"/>
                    <a-layout-content>
                        <div class="px-5 pb-5">
                            <PageLayout/>
                        </div>
                    </a-layout-content>
                    <Footer v-if="footer"/>
                </a-layout>
            </a-layout>
        </a-layout>
    </a-layout>
</template>

<script lang="ts" setup>
import {ref, computed, watch, provide} from 'vue';
import {useRouter, useRoute} from 'vue-router';
import {useAppStore, useUserStore} from '@admin/store';
import NavBar from './components/navbar/Index.vue';
import Menu from './components/menu/Index.vue';
import Footer from './components/footer/Index.vue';
import TabBar from './components/tab-bar/Index.vue';
import usePermission from '@admin/hooks/permission';
import useResponsive from '@admin/hooks/responsive';
import PageLayout from '../page/Index.vue';

const appStore = useAppStore();
const userStore = useUserStore();
const router = useRouter();
const route = useRoute();
const permission = usePermission();
useResponsive(true);
const navbarHeight = `60px`;
const navbar = computed(() => appStore.navbar);
const renderMenu = computed(() => appStore.menu);
const hideMenu = computed(() => appStore.hideMenu);
const footer = computed(() => appStore.footer);
const menuWidth = computed(() => {
    return appStore.menuCollapse ? 48 : appStore.menuWidth;
});
const collapsed = computed(() => {
    return appStore.menuCollapse;
});
const paddingStyle = computed(() => {
    const paddingLeft =
        renderMenu.value && !hideMenu.value
            ? {paddingLeft: `${menuWidth.value}px`}
            : {};
    const paddingTop = navbar.value ? {paddingTop: navbarHeight} : {};
    return {...paddingLeft, ...paddingTop};
});
const setCollapsed = (val: boolean) => {
    appStore.updateSettings({menuCollapse: val});
};
watch(
    () => userStore.role,
    (roleValue) => {
        if (roleValue && !permission.accessRouter(route))
            router.push({name: 'notFound'});
    }
);
const drawerVisible = ref(false);
const drawerCancel = () => {
    drawerVisible.value = false;
};
provide('toggleDrawerMenu', () => {
    drawerVisible.value = !drawerVisible.value;
});
</script>

