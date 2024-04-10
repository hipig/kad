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
            <div class="flex flex-col space-y-2">
                <template v-for="body in bodyList">
                    <component :is="body" />
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
        const content = item.MsgContent;
        switch (item.MsgType) {
            case 'TIMTextElem':
                return (
                    <div>{content.Text}</div>
                );
            case 'TIMLocationElem':
                return (
                    <ATooltip content={`${content.Latitude},${content.Longitude}`}>
                        <ATag>位置：{content.Desc}</ATag>
                    </ATooltip>
                );
            case 'TIMFaceElem':
                return (
                    <ATooltip content={content.Data}>
                        <ATag>表情：[{content.Index}]</ATag>
                    </ATooltip>
                );
            case 'TIMSoundElem':
                return (
                    <AButton href={content.Url}>
                        {{
                            default: () => '下载语音',
                            icon: () => <IconDownload />
                        }}
                    </AButton>
                );
            case 'TIMImageElem':
                const image = content.ImageInfoArray.find(item => item.Type === 2);
                return (
                    <AImage
                        width="300"
                        src={image?.URL}
                    />
                );
            case 'TIMFileElem':
                return (
                    <AButton href={content.Url}>
                        {{
                            default: () => '下载文件',
                            icon: () => <IconDownload />
                        }}
                    </AButton>
                );
            case 'TIMVideoFileElem':
                return (
                    <AButton href={content.VideoUrl}>
                        {{
                            default: () => '下载视频',
                            icon: () => <IconDownload />
                        }}
                    </AButton>
                );
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
