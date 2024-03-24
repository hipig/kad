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
        dataIndex: 'nickname',
        title: '昵称',
        render: ({record}) => {
            return (
                <div class="flex items-center space-x-2">
                    <img width="24" src={record.avatar} alt=""/>
                    <span>{record.nickname}</span>
                </div>
            )
        }
    },
    {
        dataIndex: 'username',
        title: '用户名'
    },
    {
        dataIndex: 'wallet_account',
        title: '钱包地址'
    },
    {
        dataIndex: 'friend_count',
        title: '好友数',
        align: 'center'
    },
    {
        dataIndex: 'following_count',
        title: '关注数',
        align: 'center'
    },
    {
        dataIndex: 'follower_count',
        title: '粉丝数',
        align: 'center'
    },
    {
        dataIndex: 'created_at',
        title: '注册时间'
    }
];

const listDataRef = ref();

const renderData = async ({ current }) => {
    return await users({
        page: current
    })
}
</script>
