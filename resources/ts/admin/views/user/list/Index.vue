<template>
    <div>
        <Breadcrumb :items="['用户管理', '用户列表']"></Breadcrumb>
        <Panel title="用户列表">
            <ListData
                ref="listDataRef"
                :render-data="renderData"
                :columns="columns"
            >
                <template #actions>
                    <AButton type="primary">添加用户</AButton>
                </template>
            </ListData>
        </Panel>
    </div>
</template>

<script lang="tsx" setup>
import {users} from "@admin/api/user";
import {ref} from "vue";
import { Message } from '@arco-design/web-vue';

const columns = [
    {
        dataIndex: 'name',
        title: '用户名称',
        render: ({record}) => {
            return (
                <div class="flex items-center space-x-2">
                    <img width="24" src={record.avatar} alt=""/>
                    <span>{record.name}</span>
                </div>
            )
        }
    },
    {
        dataIndex: 'wallet_account',
        title: '钱包地址'
    },
    {
        dataIndex: 'following_count',
        title: '关注数'
    },
    {
        dataIndex: 'following_count',
        title: '关注数'
    },
    {
        dataIndex: 'follower_count',
        title: '粉丝数'
    },
    {
        dataIndex: 'created_at',
        title: '注册时间'
    },
    {
        dataIndex: 'approved_at',
        title: '审核时间'
    }
];

const listDataRef = ref();

const storeVisible = ref(false);

const userId = ref();
const store = ref();

const renderData = async ({ current }) => {
    return await users({
        page: current
    })
}

const handleViewStore = (record) => {
    store.value = record.store;
    storeVisible.value = true;
}
</script>
