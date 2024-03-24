<template>
    <div></div>
</template>

<script lang="ts" setup>
import {useRouter, useRoute} from 'vue-router';

const router = useRouter();
const route = useRoute();

const {path: gotoPath} = route.params;
const query = route.query;
router.replace({path: gotoPath, query});
</script>
