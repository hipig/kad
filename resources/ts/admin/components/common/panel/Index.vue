<template>
    <div class="bg-white rounded shadow-sm">
        <div class="p-5">
            <slot name="title">
                <div class="text-base">{{ title }}</div>
            </slot>
        </div>
        <div class="p-5 pt-0">
            <slot></slot>
        </div>
    </div>
</template>

<script lang="ts" setup>
import {ref} from "vue";

const props = defineProps({
    title: String
})

</script>
