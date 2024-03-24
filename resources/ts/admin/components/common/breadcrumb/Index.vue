<template>
    <a-breadcrumb class="py-4">
        <a-breadcrumb-item class="text-[rgba(var(--gray-6))]">
            <icon-apps />
        </a-breadcrumb-item>
        <a-breadcrumb-item class="text-[rgba(var(--gray-6))]" v-for="item in items" :key="item">
            {{ item }}
        </a-breadcrumb-item>
    </a-breadcrumb>
</template>

<script lang="ts" setup>
import { PropType } from 'vue';

defineProps({
    items: {
        type: Array as PropType<string[]>,
        default() {
            return [];
        },
    },
});
</script>
