<template>
    <div class="h-screen">
        <div class="h-full flex items-center justify-center">
            <div class="w-full max-w-sm space-y-6">
                <div class="text-2xl">用户登录</div>
                <LoginForm/>
            </div>
            <div class="fixed bottom-0 inset-x-0">
                <Footer/>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import Footer from '@admin/layout/default/components/footer/Index.vue';
import LoginForm from './components/LoginForm.vue';
</script>
