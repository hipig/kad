<template>
    <div class="space-y-4">
        <div class="flex items-center">
            <div class="flex-auto">
                <slot name="actions" :selection-row-keys="selectionRowKeys"></slot>
            </div>
            <div class="flex-none">
                <a-space>
                    <slot name="toolbar"></slot>
                    <a-button @click="refreshData">
                        <template #icon>
                            <icon-refresh />
                        </template>
                    </a-button>
                </a-space>
            </div>
        </div>
        <a-table
            :bordered="false"
            :row-key="rowKey"
            :data="dataList"
            :columns="_columns"
            :loading="loading"
            :row-selection="_rowSelection"
            v-model:selected-keys="selectionRowKeys"
            :pagination="_pagination"
            @page-change="handlePageChange"
            @page-size-change="handlePageSizeChange"
            v-bind="$attrs"
        >
        </a-table>
    </div>
</template>
<script lang="tsx" setup>
import {computed, onMounted, ref, toRef, watch} from "vue";
import useLoading from '@admin/hooks/loading';

const props = defineProps({
    renderData: Function,
    columns: Array,
    rowSelection: {
        type: Object,
        default: {
            type: 'checkbox',
            showCheckedAll: true,
            onlyCurrent: false,
        }
    },
    pagination: {
        type: Object,
        default: {
            current: 1,
            pageSize: 15
        }
    },
    rowKey: {
        type: String,
        default: 'id'
    }
});

const slots = defineSlots();


const { loading, setLoading } = useLoading(true);

const dataList = ref([]);

const _rowSelection = ref({
    type: 'checkbox',
    showCheckedAll: true,
    onlyCurrent: false,
});
watch(
    () => props.rowSelection,
    (n) => {
        _rowSelection.value = Object.assign(_rowSelection.value, n);
    }
)

const _pagination = ref({
    current: 1,
    pageSize: 15,
    total: 0,
    hideOnSinglePage: true,
    showPageSize: true,
    pageSizeOptions: [10, 15, 20, 30, 40, 50]
});
watch(
    () => props.pagination,
    (n) => {
        _pagination.value = Object.assign(_pagination.value, n);
    }
)

const _columns = computed(() => {
    const actionColumns = slots.action ? [
        {
            dataIndex: 'action',
            title: '操作',
            render: row => {
                return (
                    <ASpace>
                        {slots.action(row)}
                    </ASpace>
                )
            }
        }
    ] : [];

    return [
        ...props.columns,
        ...actionColumns
    ];
});

const selectionRowKeys = ref([]);

onMounted(async () => {
    await getData();
})

const getData = async () => {
    setLoading(true);
    const { data, meta } = await getRenderData();
    setLoading(false);
    dataList.value = data;
    _pagination.value.total = meta.total;
}

const refreshData = async () => {
    setLoading(true);
    _pagination.value.current = 1;
    dataList.value = [];
    await getData();
}

const getRenderData = async () => {
    return props.renderData && await props.renderData(_pagination.value);
}

const handlePageChange = async (page) => {
    _pagination.value.current = page;
    await getData();
}

const handlePageSizeChange = async (pageSize) => {
    _pagination.value.pageSize = pageSize;
    await refreshData();
}

defineExpose({
    refreshData
});
</script>
