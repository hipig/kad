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
                    <AButton @click="createVisible = true" type="primary">添加群组</AButton>
                    <AButton :disabled="selectionRowKeys.length === 0" @click="handleSend(selectionRowKeys)" type="primary">发送消息</AButton>
                    <AButton :disabled="selectionRowKeys.length === 0" @click="handleDissolve(selectionRowKeys)" type="primary" status="danger">解散群组</AButton>
                </template>
                <template #action="{record}">
                    <AButton type="text" size="small" @click="handleViewUser(record.id)">查看群成员</AButton>
                    <AButton :disabled="record.status === 2" @click="handleSend([record.id])" type="text" size="small">发送消息</AButton>
                    <AButton :disabled="record.status === 2" @click="handleDissolve([record.id])" type="text" size="small">解散</AButton>
                </template>
            </ListData>
        </Panel>
        <AModal :width="640" v-model:visible="createVisible" title="添加群组" @before-ok="handleCreateGroup">
            <AForm ref="createFormRef" :model="createForm" layout="vertical">
                <AFormItem field="name" label="群名称" :rules="[{required: true, message: '群名称不能为空'}]">
                    <AInput v-model="createForm.name" placeholder="请输入群名称" allow-clear/>
                </AFormItem>
                <AFormItem field="type" label="群组类型" :rules="[{required: true, message: '群组类型不能为空'}]">
                    <ASelect v-model="createForm.type" placeholder="请选择群组类型" allow-clear>
                        <AOption v-for="(text, type) in typeMap" :value="type">{{ text }}</AOption>
                    </ASelect>
                </AFormItem>
                <AFormItem field="owner_id" label="群主">
                    <AInput v-model="createForm.owner_id" placeholder="请选择群主" allow-clear/>
                </AFormItem>
            </AForm>
        </AModal>
        <AModal :width="640" v-model:visible="sendVisible" title="发送消息" @before-ok="handleSendMessage">
            <p>发消息给共 {{ currentGroupIds.length }} 个群组？</p>
            <AForm ref="sendFormRef" :model="sendForm" layout="vertical">
                <AFormItem field="text" label="消息内容" :rules="[{required: true, message: '消息内容不能为空'}]">
                    <ATextarea v-model="sendForm.text" placeholder="请输入" allow-clear/>
                </AFormItem>
            </AForm>
        </AModal>
    </div>
</template>

<script lang="tsx" setup>
import {chatGroups, storeChatGroups, dissolveChatGroups, sendChatGroupMessages} from "@admin/api/chat-group";
import {ref} from "vue";
import {Message, Modal} from '@arco-design/web-vue';
import {useRouter} from "vue-router";

const router = useRouter();

const columns = [
    {
        dataIndex: 'group_key',
        title: '群组ID',
        width: 180
    },
    {
        dataIndex: 'name',
        title: '群组名称'
    },
    {
        dataIndex: 'type_text',
        title: '群组类型',
        width: 200
    },
    {
        dataIndex: 'owner.name',
        title: '群主',
        width: 200
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
                        <ATag color="red">解散</ATag>
                    )
            }
        }
    },
    {
        dataIndex: 'created_at',
        title: '创建时间',
        width: 180
    }
];

const typeMap = {
    Private: 'Work 好友工作群',
    Public: 'Public 陌生人社交群',
    ChatRoom: 'Meeting 临时会议群',
    AVChatRoom: 'AVChatRoom 直播群',
    Community: 'Community 社群'
}

const listDataRef = ref();

const createVisible = ref(false);

const sendVisible = ref(false);

const createFormRef = ref();

const sendFormRef = ref();

const createForm = ref({
    name: '',
    type: '',
    owner_id: ''
})

const sendForm = ref({
    text: ''
})

const currentGroupIds = ref([]);

const renderData = async ({ current }) => {
    return await chatGroups({
        page: current
    })
}

const handleViewUser = async (groupId) => {
    await router.push({
        name: 'chat-group.user',
        query: {
            group_id: groupId
        }
    })
}

const handleDissolve = (groupIds) => {
    Modal.confirm({
        title: '确定要解散所选群组？',
        content: '解散后群组所有信息将被删除，且不能恢复。',
        closable: true,
        onOk: async () => {
            await dissolveChatGroups({
                group_ids: groupIds
            });
            listDataRef.value.refreshData();
        }
    });
}

const handleSend = (groupIds) => {
    currentGroupIds.value = groupIds;
    sendVisible.value = true;
}

const handleCreateGroup = async (done) => {
    try {
        const validate = await createFormRef.value.validate();
        console.log(createFormRef.value)

        if (validate) {
            throw new Error(validate[Object.keys(validate)[0]].message || '请填写完整表单');
        }

        await storeChatGroups(createForm.value);
        done(true);
        Message.success('操作成功');
        createFormRef.value.resetFields();
        fileList.value = [];
        listDataRef.value.refreshData();
    } catch (e) {
        Message.error(e.message);
        done(false);
    }
}

const handleSendMessage = async (done) => {
    try {
        const validate = await sendFormRef.value.validate();

        if (validate) {
            throw new Error(validate[Object.keys(validate)[0]].message || '请填写完整表单');
        }

        await sendChatGroupMessages({
            group_ids: currentGroupIds.value,
            text: sendForm.value.text
        });
        done(true);
        Message.success('操作成功');
        sendFormRef.value.resetFields();
        listDataRef.value.refreshData();
    } catch (e) {
        Message.error(e.message);
        done(false);
    }
}

const getDisabledSelection = (record) => {
    return record.status === 2;
}
</script>
