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
        <AModal :width="640" v-model:visible="bodyViewVisible" title="内容详情" :footer="false">
            <div class="space-y-2">
                <template v-for="({type, content}, _) in bodyList">
                    <div v-if="type === 'TIMTextElem'">{{ content.Text }}</div>
                    <ATooltip v-if="type === 'TIMLocationElem'" :content="`${content.Latitude},${content.Longitude}`">
                        <ATag>位置：{{content.Desc}}</ATag>
                    </ATooltip>
                    <ATooltip v-if="type === 'TIMFaceElem'" :content="content.Data">
                        <ATag>表情：[{{content.Index}}]</ATag>
                    </ATooltip>
                    <AButton v-if="type === 'TIMSoundElem'" :href="content.Url">
                        <template #icon>
                            <IconDownload />
                        </template>
                        <span>下载语音</span>
                    </AButton>
                    <template v-if="type === 'TIMImageElem'">
                        <AImage
                            height="200"
                            :src="content?.URL"
                        />
                    </template>
                    <AButton v-if="type === 'TIMFileElem'" :href="content.Url">
                        <template #icon>
                            <IconDownload />
                        </template>
                        <span>下载文件</span>
                    </AButton>
                    <AButton v-if="type === 'TIMVideoFileElem'" :href="content.VideoUrl">
                        <template #icon>
                            <IconDownload />
                        </template>
                        <span>下载视频</span>
                    </AButton>
                </template>
            </div>
        </AModal>
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
            return (
                <AButton size="small" onClick={() => handleViewBody(record)}>查看详情</AButton>
            );
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

const bodyViewVisible = ref(false);

const bodyList = ref([]);

const renderData = async ({ current }) => {
    return await chatGroupMessages({
        page: current
    })
}

const handleViewBody = (record) => {
    bodyList.value = record.body.map(item => {
        let content = item.MsgContent;
        const type = item.MsgType;
        if (type === 'TIMImageElem') {
            content = content.ImageInfoArray.find(item => item.Type === 2);
        }

        return {
            type,
            content
        }
    });
    bodyViewVisible.value = true;
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
