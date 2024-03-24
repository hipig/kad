<template>
    <div>
        <Breadcrumb :items="['动态管理', '评论列表']"></Breadcrumb>
        <Panel title="评论列表">
            <ListData
                ref="listDataRef"
                :render-data="renderData"
                :columns="columns"
            >
            </ListData>
        </Panel>
    </div>
</template>

<script lang="tsx" setup>
import {postComments} from "@admin/api/post";
import {ref} from "vue";
import { Message } from '@arco-design/web-vue';

const columns = [
    {
        dataIndex: 'user',
        title: '发布用户',
        render: ({record}) => {
            return (
                <div class="flex items-center space-x-2">
                    <img width="24" src={record.user.avatar} alt=""/>
                    <span>{record.user.nickname}</span>
                </div>
            )
        }
    },
    {
        dataIndex: 'post.content',
        title: '动态内容'
    },
    {
        dataIndex: 'content',
        title: '评论内容'
    },
    {
        dataIndex: 'created_at',
        title: '创建时间'
    }
];

const listDataRef = ref();

const renderData = async ({ current }) => {
    return await postComments({
        page: current
    })
}
</script>
