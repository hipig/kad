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
                            <a-space @click="handleChangePassword">
                                <icon-lock/>
                                <span>修改密码</span>
                            </a-space>
                        </a-doption>
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
        <AModal :width="640" v-model:visible="passwordVisible" title="修改密码" @before-ok="handleChangePasswordConfirm">
            <AForm ref="passwordFormRef" :model="passwordForm" layout="vertical">
                <AFormItem field="old_password" label="当前密码" :rules="[{required: true, message: '当前密码不能为空'}]">
                    <AInput type="password" v-model="passwordForm.old_password" placeholder="请输入当前密码" allow-clear/>
                </AFormItem>
                <AFormItem field="password" label="新密码" :rules="[{required: true, message: '新密码不能为空'}]">
                    <AInput type="password" v-model="passwordForm.password" placeholder="请输入新密码" allow-clear/>
                </AFormItem>
                <AFormItem field="password_confirmation" label="重复新密码" :rules="[{required: true, message: '重复新密码不能为空'}]">
                    <AInput type="password" v-model="passwordForm.password_confirmation" placeholder="请输入重复新密码" allow-clear/>
                </AFormItem>
            </AForm>
        </AModal>
    </div>
</template>

<script lang="ts" setup>
import {computed, ref, inject} from 'vue';
import {Message} from '@arco-design/web-vue';
import {useDark, useToggle, useFullscreen} from '@vueuse/core';
import {useAppStore, useUserStore} from '@admin/store';
import useUser from '@admin/hooks/user';
import {changeMePassword} from "@admin/api/admin-user";
import {useRouter} from "vue-router";

const router = useRouter();

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

const passwordVisible = ref(false);

const passwordFormRef = ref();
const passwordForm = ref({
    old_password: '',
    password: '',
    password_confirmation: '',
})

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

const handleChangePassword = () => {
    passwordVisible.value = true;
}

const handleChangePasswordConfirm = async (done) => {
    try {
        const validate = await passwordFormRef.value.validate();

        if (validate) {
            throw new Error(validate[Object.keys(validate)[0]].message || '请填写完整表单');
        }

        await changeMePassword(passwordForm.value);
        done(true);
        Message.success('操作成功，请重新登录');
        await passwordFormRef.value.resetFields();
        await userStore.logout();
        await router.go(0);
    } catch (e) {
        done(false);
    }
}

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
