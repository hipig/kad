<template>
    <div>
        <Breadcrumb :items="['聊天管理', '单聊消息']"></Breadcrumb>
        <Panel title="单聊消息">
            <ListData
                ref="listDataRef"
                :render-data="renderData"
                :columns="columns"
                :selection-disabled-method="getDisabledSelection"
            >
                <template #actions="{selectionRowKeys}">
                    <AButton @click="createVisible = true" type="primary">发送消息</AButton>
                    <AButton :disabled="selectionRowKeys.length === 0" @click="handleWithdraw(selectionRowKeys)" type="primary">批量撤回</AButton>
                </template>
                <template #action="{record}">
                    <AButton :disabled="record.status === 2" @click="handleWithdraw([record.id])" type="text" size="small">撤回</AButton>
                </template>
            </ListData>
        </Panel>
        <AModal :width="640" v-model:visible="createVisible" title="发送消息" @before-ok="handleCreateMessage">
            <AForm ref="createFormRef" :model="createForm" layout="vertical">
                <AFormItem field="to_user_ids" label="接收用户" :rules="[{required: true, message: '接收用户不能为空'}]">
                    <ASelect v-model="createForm.to_user_ids" :options="userOptions" :loading="userLoading" placeholder="请选择新成员" multiple @search="handleUserSearch" :filter-option="false" />
                </AFormItem>
                <AFormItem field="text" label="消息内容" :rules="[{required: true, message: '消息内容不能为空'}]">
                    <ATextarea v-model="createForm.text" placeholder="请输入" allow-clear/>
                </AFormItem>
            </AForm>
        </AModal>
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
import {chatMessages, storeChatMessages, withdrawChatMessages} from "@admin/api/chat-message";
import {ref, computed, onMounted} from "vue";
import {Message, Modal} from '@arco-design/web-vue';
import {useRouter} from "vue-router";
import {users} from "@admin/api/user";

const router = useRouter();

const columns = [
    {
        dataIndex: 'msg_seq',
        title: '消息KEY',
        width: 120
    },
    {
        dataIndex: 'from_user',
        title: '发送用户',
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
        dataIndex: 'to_user',
        title: '接收用户',
        render: ({record}) => {
            return (
                <div class="flex items-center space-x-2">
                    <AImage width="24" src={record.to_user.avatar} alt=""/>
                    <span>{record.to_user.nickname}</span>
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

const createVisible = ref(false);

const createFormRef = ref();

const createForm = ref({
    to_user_ids: [],
    text: ''
})

const userLoading = ref(false);

const userList = ref([]);

const userOptions = computed(() => {
    return userList.value.map(item => {
        return {
            value: item.id,
            label: item.nickname + '-' + item.wallet_account
        }
    })
})

const bodyViewVisible = ref(false);

const bodyList = ref([]);

onMounted(async () => {
    await getUserList();
})

const getUserList = async (keyword = '') => {
    const res = await users({
        keyword,
        page_size: 20
    });
    userList.value = res.data;
}

const renderData = async ({ current }) => {
    return await chatMessages({
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

const handleCreateMessage = async (done) => {
    try {
        const validate = await createFormRef.value.validate();

        if (validate) {
            throw new Error(validate[Object.keys(validate)[0]].message || '请填写完整表单');
        }

        await storeChatMessages(createForm.value);
        done(true);
        Message.success('操作成功');
        createFormRef.value.resetFields();
        listDataRef.value.refreshData();
    } catch (e) {
        Message.error(e.message);
        done(false);
    }
}

const handleWithdraw = (messageIds) => {
    Modal.confirm({
        title: '确定要撤回所选消息？',
        content: '撤回后将不能恢复。',
        closable: true,
        onOk: async () => {
            await withdrawChatMessages({
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
