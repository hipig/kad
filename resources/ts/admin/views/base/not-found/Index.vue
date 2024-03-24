<template>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center">
        <a-result class="result" status="404" :subtitle="'not found'"></a-result>
        <div class="operation-row">
            <a-button key="back" type="primary" @click="back"> back</a-button>
        </div>
    </div>
</template>

<script lang="ts" setup>
import {useRouter} from 'vue-router';

const router = useRouter();
const back = () => {
    // warning： Go to the node that has the permission
    router.push({name: 'Workplace'});
};
</script>
