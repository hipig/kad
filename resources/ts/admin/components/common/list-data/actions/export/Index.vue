<template>
    <AButton type="primary" status="success" @click="downloadData">
        <template #icon>
            <IconExport/>
        </template>
        <span>导出</span>
    </AButton>
</template>

<script lang="ts" setup>
import {ref} from "vue";

const props = defineProps({
    exportData: Function,
    fileName: String,
    extension: {
        type: String,
        default: 'xlsx'
    }
})

const downloadData = async () => {
    const res = await getExportData();
    const url = window.URL.createObjectURL(new Blob([res]));
    const link = document.createElement('a');
    link.href = url;
    link.style.display = 'none';
    link.download = `${props.fileName}_${new Date().getTime()}.${props.extension}`;
    document.body.appendChild(link);
    link.click();
    link.parentNode.removeChild(link);
    window.URL.revokeObjectURL(url);
}

const getExportData = async () => {
    return await props.exportData && props.exportData();
}
</script>
