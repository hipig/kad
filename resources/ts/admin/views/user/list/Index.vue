<template>
    <div>
        <Breadcrumb :items="['用户管理', '用户列表']"></Breadcrumb>
        <Panel title="用户列表">
            <ListData
                ref="listDataRef"
                :render-data="renderData"
                :columns="columns"
            >
                <template #actions>
                    <AButton @click="createVisible = true" type="primary">添加用户</AButton>
                </template>
                <template #action="{record}">
                    <AButton type="text" size="small">编辑</AButton>
                </template>
            </ListData>
        </Panel>
        <AModal :width="640" v-model:visible="createVisible" title="添加用户" @before-ok="handleBeforeOk">
            <AForm ref="createFormRef" :model="createForm" layout="vertical">
                <AFormItem field="wallet_account" label="钱包地址" :rules="[{required: true, message: '钱包地址不能为空'}]">
                    <AInput v-model="createForm.wallet_account" placeholder="请输入钱包地址" />
                </AFormItem>
                <AFormItem field="nickname" label="昵称">
                    <AInput v-model="createForm.nickname" placeholder="请输入昵称" />
                </AFormItem>
                <AFormItem field="avatar" label="头像">
                    <AUpload v-model:file-list="fileList" @success="handleFileUpload" action="/admin/api/uploads" :limit="1" accept="image/*" />
                </AFormItem>
            </AForm>
        </AModal>
    </div>
</template>

<script lang="tsx" setup>
import {users, storeUsers} from "@admin/api/user";
import {ref} from "vue";
import { Message } from '@arco-design/web-vue';

const columns = [
    {
        dataIndex: 'nickname',
        title: '昵称',
        render: ({record}) => {
            return (
                <div class="flex items-center space-x-2">
                    <AImage width="24" src={record.avatar} alt=""/>
                    <span>{record.nickname}</span>
                </div>
            )
        }
    },
    {
        dataIndex: 'username',
        title: '用户名'
    },
    {
        dataIndex: 'wallet_account',
        title: '钱包地址'
    },
    {
        dataIndex: 'friend_count',
        title: '好友数',
        align: 'center'
    },
    {
        dataIndex: 'following_count',
        title: '关注数',
        align: 'center'
    },
    {
        dataIndex: 'follower_count',
        title: '粉丝数',
        align: 'center'
    },
    {
        dataIndex: 'created_at',
        title: '注册时间'
    }
];

const listDataRef = ref();

const createVisible = ref(false);

const createFormRef = ref();

const createForm = ref({
    nickname: '',
    avatar: '',
    wallet_account: ''
})

const fileList = ref([]);

const renderData = async ({ current }) => {
    return await users({
        page: current
    })
}

const handleBeforeOk = async (done) => {
    try {
        const validate = await createFormRef.value.validate();
        console.log(createFormRef.value)

        if (validate) {
            throw new Error(validate[Object.keys(validate)[0]].message || '请填写完整表单');
        }

        await storeUsers(createForm.value);
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

const handleFileUpload = (file) => {
    const response = file?.response;
    if (response) {
        createForm.value.avatar = response.path;
    }
}
</script>
