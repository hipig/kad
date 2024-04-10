<template>
    <div>
        <Breadcrumb :items="['聊天管理', '群组列表']"></Breadcrumb>
        <Panel title="群组列表">
            <ListData
                ref="listDataRef"
                :render-data="renderData"
                :columns="columns"
                :selection-disabled-method="getDisabledSelection"
            >
                <template #actions="{selectionRowKeys}">
                    <AButton :disabled="selectionRowKeys.length === 0" @click="handleRecall(selectionRowKeys)" type="primary">批量撤回</AButton>
                </template>
                <template #action="{record}">
                    <AButton :disabled="record.status === 2" @click="handleRecall([record.id])" type="text" size="small">撤回</AButton>
                </template>
            </ListData>
        </Panel>
    </div>
</template>

<script lang="tsx" setup>
import {chatGroupMessages, recallChatGroupMessages} from "@admin/api/chat-group";
import {ref} from "vue";
import {Message, Modal} from '@arco-design/web-vue';
import {useRouter} from "vue-router";

const router = useRouter();

const columns = [
    {
        dataIndex: 'msg_seq',
        title: '消息KEY',
        width: 120
    },
    {
        dataIndex: 'group.name',
        title: '群组',
        width: 180
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
        dataIndex: 'body',
        title: '消息内容',
        render: ({record}) => {
            const bodyList = record.body.map(item => {
                switch (item.MsgType) {
                    case 'TIMTextElem':
                        return item.MsgContent.Text;
                }
            });
            return bodyList.join('');
        }
    },
    {
        dataIndex: 'status',
        title: '状态',
        width: 120,
        render: ({record}) => {
            switch (record.status) {
                case 1:
                    return (
                        <ATag color="arcoblue">正常</ATag>
                    )
                case 2:
                    return (
                        <ATag color="red">撤回</ATag>
                    )
            }
        }
    },
    {
        dataIndex: 'sent_at',
        title: '发言时间',
        width: 180
    }
];

const listDataRef = ref();

const renderData = async ({ current }) => {
    return await chatGroupMessages({
        page: current
    })
}

const handleRecall = (messageIds) => {
    Modal.confirm({
        title: '确定要撤回所选消息？',
        content: '撤回后将不能恢复。',
        closable: true,
        onOk: async () => {
            await recallChatGroupMessages({
                message_ids: messageIds
            });
            listDataRef.value.refreshData();
        }
    });
}

const getDisabledSelection = (record) => {
    return record.status === 2;
}
</script>
