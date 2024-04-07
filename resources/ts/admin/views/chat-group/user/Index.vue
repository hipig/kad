<template>
    <div>
        <Breadcrumb :items="['聊天管理', '群组成员']"></Breadcrumb>
        <Panel title="群组成员">
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
import {chatGroupUsers} from "@admin/api/chat-group";
import {ref} from "vue";
import {useRoute} from "vue-router";

const route = useRoute();

const columns = [
    {
        dataIndex: 'group.name',
        title: '群组名称'
    },
    {
        dataIndex: 'user',
        title: '用户',
        render: ({record}) => {
            return (
                <div class="flex items-center space-x-2">
                    <AImage width="24" src={record.user.avatar} alt=""/>
                    <span>{record.user.nickname}</span>
                </div>
            )
        }
    },
    {
        dataIndex: 'role_text',
        title: '角色',
        width: 200
    },
    {
        dataIndex: 'join_at',
        title: '入群时间',
        width: 180
    },
    {
        dataIndex: 'last_send_msg_at',
        title: '最后发言时间',
        width: 200
    }
];

const filterForm = ref({
    group_id: route.query.group_id
});

const listDataRef = ref();

const renderData = async ({ current }) => {
    return await chatGroupUsers({
        page: current,
        ...filterForm.value
    })
}
</script>
