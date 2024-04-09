<template>
    <div>
        <Breadcrumb :items="['聊天管理', '群组成员']"></Breadcrumb>
        <Panel title="群组成员">
            <ListData
                ref="listDataRef"
                :render-data="renderData"
                :columns="columns"
            >
                <template #actions>
                    <AButton type="primary" @click="addUserVisible = true">添加群成员</AButton>
                </template>
            </ListData>
        </Panel>
        <AModal :width="640" v-model:visible="addUserVisible" title="添加群成员" @before-ok="handleAddUser">
            <AForm ref="addUserFormRef" :model="addUserForm" layout="vertical">
                <AFormItem field="user_ids" label="新成员" :rules="[{required: true, message: '新成员不能为空'}]">
                    <ASelect v-model="addUserForm.user_ids" :options="userOptions" :loading="userLoading" placeholder="请选择新成员" multiple @search="handleUserSearch" :filter-option="false" />
                </AFormItem>
            </AForm>
        </AModal>
    </div>
</template>

<script lang="tsx" setup>
import {chatGroupUsers, joinChatGroups} from "@admin/api/chat-group";
import {computed, onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import {users} from "@admin/api/user";
import {Message} from "@arco-design/web-vue";

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

const groupId = ref(route.query.group_id);

const filterForm = ref({
    group_id: groupId.value
});

const listDataRef = ref();

const addUserVisible = ref(false);

const addUserFormRef = ref();

const addUserForm = ref({
    user_ids: []
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
    await getUserList();
})

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
</script>
