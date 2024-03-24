<template>
    <div>
        <Breadcrumb :items="['动态管理', '动态列表']"></Breadcrumb>
        <Panel title="动态列表">
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
import {posts} from "@admin/api/post";
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
        dataIndex: 'content',
        title: '内容'
    },
    {
        dataIndex: 'visible_status_text',
        title: '可见状态'
    },
    {
        dataIndex: 'comment_count',
        title: '评论数',
        align: 'center'
    },
    {
        dataIndex: 'collect_count',
        title: '收藏数',
        align: 'center'
    },
    {
        dataIndex: 'like_count',
        title: '点赞数',
        align: 'center'
    },
    {
        dataIndex: 'view_count',
        title: '浏览数',
        align: 'center'
    },
    {
        dataIndex: 'created_at',
        title: '创建时间'
    }
];

const listDataRef = ref();

const renderData = async ({ current }) => {
    return await posts({
        page: current
    })
}
</script>
