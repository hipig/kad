<template>
    <div>
        <Breadcrumb :items="['聊天管理', '群组详情']"></Breadcrumb>
        <div class="space-y-6">
            <Panel title="群基本信息">
                <template #action>
                    <AButton type="text" :disabled="group.status === 2" size="small" @click="handleEdit">编辑</AButton>
                </template>
                <div class="w-full flex flex-col space-y-4">
                    <div class="flex items-center">
                        <div class="flex-none">
                            <div class="w-32 text-gray-400">群ID</div>
                        </div>
                        <div class="flex-auto">
                            <div>{{ group.group_key }}</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex-none">
                            <div class="w-32 text-gray-400">群名称</div>
                        </div>
                        <div class="flex-auto">
                            <div>{{ group.name }}</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex-none">
                            <div class="w-32 text-gray-400">群组类型</div>
                        </div>
                        <div class="flex-auto">
                            <div>{{ group.type_text }}</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex-none">
                            <div class="w-32 text-gray-400">群简介</div>
                        </div>
                        <div class="flex-auto">
                            <div>{{ group.introduction || '-' }}</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex-none">
                            <div class="w-32 text-gray-400">群通知</div>
                        </div>
                        <div class="flex-auto">
                            <div>{{ group.notification || '-' }}</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex-none">
                            <div class="w-32 text-gray-400">群主</div>
                        </div>
                        <div class="flex-auto">
                            <div class="flex items-center space-x-2">
                                <div>{{ group.owner?.nickname || '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex-none">
                            <div class="w-32 text-gray-400">创建时间</div>
                        </div>
                        <div class="flex-auto">
                            <div>{{ group.created_at }}</div>
                        </div>
                    </div>
                </div>
            </Panel>
            <Panel title="群成员管理">
                <ListData
                    ref="listDataRef"
                    :render-data="renderData"
                    :columns="columns"
                    :selection-disabled-method="getDisabledSelection"
                >
                    <template #actions="{selectionRowKeys}">
                        <AButton type="primary" :disabled="group.status === 2" @click="addUserVisible = true">添加群成员</AButton>
                        <AButton :disabled="selectionRowKeys.length === 0" @click="handleRemoveUser(selectionRowKeys)" type="primary" status="danger">删除群成员</AButton>
                    </template>
                    <template #action="{record}">
                        <AButton :disabled="record.status === 2" type="text" size="small" @click="handleRemoveUser([record.id])">删除群成员</AButton>
                    </template>
                </ListData>
            </Panel>
        </div>
        <AModal :width="640" v-model:visible="addUserVisible" title="添加群成员" @before-ok="handleAddUser">
            <AForm ref="addUserFormRef" :model="addUserForm" layout="vertical">
                <AFormItem field="user_ids" label="新成员" :rules="[{required: true, message: '新成员不能为空'}]">
                    <ASelect v-model="addUserForm.user_ids" :options="userOptions" :loading="userLoading" placeholder="请选择新成员" multiple @search="handleUserSearch" :filter-option="false" />
                </AFormItem>
            </AForm>
        </AModal>
        <AModal :width="640" v-model:visible="updateVisible" title="添加群成员" @before-ok="handleUpdateGroup">
            <AForm ref="updateFormRef" :model="updateForm" layout="vertical">
                <AFormItem field="name" label="群名称" :rules="[{required: true, message: '群名称不能为空'}]">
                    <AInput v-model="updateForm.name" placeholder="请输入群名称" allow-clear/>
                </AFormItem>
                <AFormItem field="introduction" label="群描述">
                    <ATextarea rows="2" v-model="updateForm.introduction" placeholder="请输入群描述" allow-clear/>
                </AFormItem>
                <AFormItem field="notification" label="群通知">
                    <ATextarea rows="2" v-model="updateForm.notification" placeholder="请输入群通知" allow-clear/>
                </AFormItem>
            </AForm>
        </AModal>
    </div>
</template>

<script lang="tsx" setup>
import {showChatGroups, updateChatGroups, chatGroupUsers, joinChatGroups, exitChatGroups} from "@admin/api/chat-group";
import {computed, onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import {users} from "@admin/api/user";
import {Message, Modal} from "@arco-design/web-vue";
import {cloneDeep} from "lodash";

const route = useRoute();

const columns = [
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
                        <ATag color="red">退出</ATag>
                    )
            }
        }
    },
];

const groupId = ref(route.query.group_id);

const filterForm = ref({
    group_id: groupId.value
});

const listDataRef = ref();

const group = ref({});

const addUserVisible = ref(false);

const addUserFormRef = ref();

const addUserForm = ref({
    user_ids: []
})

const updateVisible = ref(false);

const updateFormRef = ref();

const updateForm = ref({
    name: '',
    introduction: '',
    notification: ''
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

onMounted(async () => {
    await getChatGroupDetail();
    await getUserList();
})

const getChatGroupDetail = async () => {
    group.value = await showChatGroups(groupId.value);
}

const getUserList = async (keyword = '') => {
    const res = await users({
        keyword,
        page_size: 20
    });
    userList.value = res.data;
}

const handleUserSearch = async (value) => {
    userLoading.value = true;
    await getUserList(value);
    userLoading.value = false;
}

const renderData = async ({ current }) => {
    return await chatGroupUsers({
        page: current,
        ...filterForm.value
    })
}

const handleAddUser = async (done) => {
    try {
        const validate = await addUserFormRef.value.validate();

        if (validate) {
            throw new Error(validate[Object.keys(validate)[0]].message || '请填写完整表单');
        }

        await joinChatGroups(groupId.value, addUserForm.value);
        done(true);
        Message.success('操作成功');
        addUserFormRef.value.resetFields();
        listDataRef.value.refreshData();
    } catch (e) {
        Message.error(e.message);
        done(false);
    }
}

const handleRemoveUser = async (userIds) => {
    Modal.confirm({
        title: '删除群成员',
        content: `确定删除 ${userIds.length} 个群成员？`,
        closable: true,
        onOk: async () => {
            await exitChatGroups(groupId.value, {
                group_user_ids: userIds
            });
            Message.success('操作成功');
            listDataRef.value.refreshData();
        }
    });
}

const handleEdit = () => {
    updateForm.value = cloneDeep(group.value);
    updateVisible.value = true;
}

const handleUpdateGroup = async () => {
    await updateChatGroups(groupId.value, updateForm.value);
    await getChatGroupDetail();
}

const getDisabledSelection = (record) => {
    return record.status === 2;
}
</script>
