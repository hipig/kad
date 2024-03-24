<template>
    <div class="h-full flex justify-between bg-[var(--color-bg-2)] border-b border-solid border-[var(--color-border)]">
        <div class="flex items-center pl-5">
            <a-space :size="20">
                <a href="/admin" style="text-decoration: none">
                    <a-space>
                        <h3 class="text-lg"><b>{{ appName }}</b> 后台管理</h3>
                    </a-space>
                </a>
                <icon-menu-fold
                    v-if="appStore.device === 'mobile'"
                    style="font-size: 22px; cursor: pointer"
                    @click="toggleDrawerMenu"
                />
            </a-space>
        </div>
        <ul class="flex items-center pr-5 space-x-5">
            <li>
                <a-tooltip
                    :content="
            isFullscreen
              ? '点击退出全屏模式'
              : '点击切换全屏模式'
          "
                >
                    <a-button
                        class="text-base !border-gray-200 !text-[rgb(var(--gray-8))]"
                        type="outline"
                        :shape="'circle'"
                        @click="toggleFullScreen"
                    >
                        <template #icon>
                            <icon-fullscreen-exit v-if="isFullscreen"/>
                            <icon-fullscreen v-else/>
                        </template>
                    </a-button>
                </a-tooltip>
            </li>
            <li>
                <a-dropdown trigger="click" position="br">
                    <a-avatar
                        :size="32"
                        :style="{ marginRight: '8px', cursor: 'pointer' }"
                    >
                        {{ name }}
                    </a-avatar>
                    <template #content>
                        <a-doption>
                            <a-space @click="handleLogout">
                                <icon-export/>
                                <span>退出登录</span>
                            </a-space>
                        </a-doption>
                    </template>
                </a-dropdown>
            </li>
        </ul>
    </div>
</template>

<script lang="ts" setup>
import {computed, ref, inject} from 'vue';
import {Message} from '@arco-design/web-vue';
import {useDark, useToggle, useFullscreen} from '@vueuse/core';
import {useAppStore, useUserStore} from '@admin/store';
import useUser from '@admin/hooks/user';

const appStore = useAppStore();
const userStore = useUserStore();
const {logout} = useUser();
const {isFullscreen, toggle: toggleFullScreen} = useFullscreen();
const appName = computed(() => {
    return import.meta.env.VITE_APP_NAME;
});
const name = computed(() => {
    return userStore.userInfo.name?.substring(0, 1);
});
const avatar = computed(() => {
    return userStore.userInfo.avatar;
});
const theme = computed(() => {
    return appStore.theme;
});
const isDark = useDark({
    selector: 'body',
    attribute: 'arco-theme',
    valueDark: 'dark',
    valueLight: 'light',
    storageKey: 'arco-theme',
    onChanged(dark: boolean) {
        // overridden default behavior
        appStore.toggleTheme(dark);
    },
});
const toggleTheme = useToggle(isDark);
const handleToggleTheme = () => {
    toggleTheme();
};

const visible = ref(false);
const setVisible = () => {
    visible.value = true;
};
const refBtn = ref();
const triggerBtn = ref();
const setPopoverVisible = () => {
    const event = new MouseEvent('click', {
        view: window,
        bubbles: true,
        cancelable: true,
    });
    refBtn.value.dispatchEvent(event);
};
const handleLogout = () => {
    logout();
};
const setDropDownVisible = () => {
    const event = new MouseEvent('click', {
        view: window,
        bubbles: true,
        cancelable: true,
    });
    triggerBtn.value.dispatchEvent(event);
};
const toggleDrawerMenu = inject('toggleDrawerMenu');
</script>

<style lang="css">
.message-popover .arco-popover-content {
    margin-top: 0;
}
</style>
