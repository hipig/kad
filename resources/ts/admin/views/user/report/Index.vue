<template>
    <div>
        <Breadcrumb :items="['用户管理', '用户举报']"></Breadcrumb>
        <Panel title="用户举报">
            <ListData
                ref="listDataRef"
                :render-data="renderData"
                :columns="columns"
            >
                <template #actions>
                    <AButton type="primary">批量处理</AButton>
                </template>
                <template #action="{record}">
                    <AButton type="text" size="small">处理</AButton>
                </template>
            </ListData>
        </Panel>
    </div>
</template>

<script lang="tsx" setup>
import {reports} from "@admin/api/report";
import {ref} from "vue";
import { Message } from '@arco-design/web-vue';

const columns = [
    {
        dataIndex: 'nickname',
        title: '被举报用户',
        render: ({record}) => {
            return (
                <div class="flex items-center space-x-2">
                    <img width="24" src={record.reportable.avatar} alt=""/>
                    <span>{record.reportable.nickname}</span>
                </div>
            )
        }
    },
    {
        dataIndex: 'type',
        title: '举报类型'
    },
    {
        dataIndex: 'content',
        title: '举报内容'
    },
    {
        dataIndex: 'user.nickname',
        title: '举报人'
    },
    {
        dataIndex: 'created_at',
        title: '创建时间'
    }
];

const listDataRef = ref();

const renderData = async ({ current }) => {
    return await reports({
        page: current,
        reportable_type: 'USER'
    })
}
</script>
