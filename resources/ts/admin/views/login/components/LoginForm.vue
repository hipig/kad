<template>
    <a-form
        ref="loginFormRef"
        :model="loginForm"
        class="login-form"
        layout="vertical"
        @submit="handleSubmit"
    >
        <a-form-item
            field="username"
            :rules="[{ required: true, message: '用户名不能为空' }]"
            hide-label
        >
            <a-input
                v-model="loginForm.username"
                placeholder="用户名"
            >
                <template #prefix>
                    <icon-user/>
                </template>
            </a-input>
        </a-form-item>
        <a-form-item
            field="password"
            :rules="[{ required: true, message: '密码不能为空' }]"
            hide-label
        >
            <a-input
                type="password"
                v-model="loginForm.password"
                placeholder="密码"
            >
                <template #prefix>
                    <icon-lock/>
                </template>
            </a-input>
        </a-form-item>
        <a-space :size="16" direction="vertical">
            <a-button type="primary" html-type="submit" long :loading="loading">
                立即登录
            </a-button>
        </a-space>
    </a-form>
</template>

<script lang="ts" setup>
import {ref, reactive, computed, unref} from 'vue';
import {useRouter} from 'vue-router';
import {Message, Notification} from '@arco-design/web-vue';
import {ValidatedError} from '@arco-design/web-vue/es/form/interface';
import {useUserStore} from '@admin/store';
import useLoading from '@admin/hooks/loading';
import {LoginData} from '@admin/api/authorization';

const router = useRouter();
const errorMessage = ref('');
const {loading, setLoading} = useLoading();
const userStore = useUserStore();

const loginFormRef = ref();
const loginForm = ref({
    username: '',
    password: ''
})

const handleSubmit = async ({
                                errors,
                                values,
                            }: {
    errors: Record<string, ValidatedError> | undefined;
    values: Record<string, any>;
}) => {
    if (!errors) {
        setLoading(true);
        try {
            await userStore.login(values as LoginData);
            const {redirect, ...othersQuery} = router.currentRoute.value.query;
            await router.push({
                name: (redirect as string) || 'workplace.dashboard',
                query: {
                    ...othersQuery,
                }
            });
            Notification.success('登录成功');
        } catch (err) {
            errorMessage.value = (err as Error).message;
        } finally {
            setLoading(false);
        }
    }
};

</script>
