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
                    <AButton @click="handleUpdate(record)" type="text" size="small">编辑</AButton>
                </template>
            </ListData>
        </Panel>
        <AModal :width="640" v-model:visible="createVisible" title="添加用户" @before-ok="handleCreateUser">
            <AForm ref="createFormRef" :model="createForm" layout="vertical">
                <AFormItem field="wallet_account" label="钱包地址" :rules="[{required: true, message: '钱包地址不能为空'}]">
                    <AInput v-model="createForm.wallet_account" placeholder="请输入钱包地址" />
                </AFormItem>
                <AFormItem field="nickname" label="昵称">
                    <AInput v-model="createForm.nickname" placeholder="请输入昵称" />
                </AFormItem>
                <AFormItem field="avatar" label="头像">
                    <AUpload list-type="picture-card" v-model:file-list="fileList" @success="handleFileUpload" action="/admin/api/uploads" :limit="1" />
                </AFormItem>
            </AForm>
        </AModal>
        <AModal :width="640" v-model:visible="updateVisible" title="编辑用户" @before-ok="handleUpdateUser">
            <AForm ref="updateFormRef" :model="updateForm" layout="vertical">
                <AFormItem field="username" label="用户名">
                    <AInput v-model="updateForm.username" placeholder="请输入用户名" readonly/>
                </AFormItem>
                <AFormItem field="wallet_account" label="钱包地址" :rules="[{required: true, message: '钱包地址不能为空'}]">
                    <AInput v-model="updateForm.wallet_account" placeholder="请输入钱包地址" readonly/>
                </AFormItem>
                <AFormItem field="nickname" label="昵称" :rules="[{required: true, message: '昵称不能为空'}]">
                    <AInput v-model="updateForm.nickname" placeholder="请输入昵称" />
                </AFormItem>
                <AFormItem field="avatar" label="头像">
                    <AUpload list-type="picture-card" v-model:file-list="updateFileList" @success="handleUpdateFileUpload" action="/admin/api/uploads" :limit="1"/>
                </AFormItem>
                <AFormItem field="gender" label="性别">
                    <ASelect v-model="updateForm.gender" placeholder="请选择性别">
                        <AOption value="Gender_Type_Unknown">未设置性别</AOption>
                        <AOption value="Gender_Type_Female">女</AOption>
                        <AOption value="Gender_Type_Male">男</AOption>
                    </ASelect>
                </AFormItem>
                <AFormItem field="location" label="所在地">
                    <AInput v-model="updateForm.location" placeholder="请输入所在地" />
                </AFormItem>
                <AFormItem field="birthday" label="生日">
                    <AInputNumber v-model="updateForm.birthday" placeholder="格式为：20200101" />
                </AFormItem>
                <AFormItem field="self_signature" label="个性签名">
                    <AInput v-model="updateForm.self_signature" placeholder="请输入个性签名" />
                </AFormItem>
                <AFormItem field="allow_type" label="加好友验证方式">
                    <ASelect v-model="updateForm.allow_type" placeholder="请选择状态">
                        <AOption value="AllowType_Type_NeedConfirm">需要经过自己确认对方才能添加自己为好友</AOption>
                        <AOption value="AllowType_Type_AllowAny">允许任何人添加自己为好友</AOption>
                        <AOption value="AllowType_Type_DenyAny">不允许任何人添加自己为好友</AOption>
                    </ASelect>
                </AFormItem>
                <AFormItem field="language" label="语言">
                    <AInput v-model="updateForm.language" placeholder="请输入语言" />
                </AFormItem>
                <AFormItem field="level" label="等级">
                    <AInput v-model="updateForm.level" placeholder="请输入等级" />
                </AFormItem>
                <AFormItem field="role" label="角色">
                    <AInput v-model="updateForm.role" placeholder="请输入角色" />
                </AFormItem>
                <AFormItem field="admin_forbid_type" label="管理员禁止加好友标识">
                    <ASelect v-model="updateForm.admin_forbid_type" placeholder="请选择状态">
                        <AOption value="AdminForbid_Type_None">需要经过自己确认对方才能添加自己为好友</AOption>
                        <AOption value="AdminForbid_Type_SendOut">禁止该用户发起加好友请求</AOption>
                    </ASelect>
                </AFormItem>
                <AFormItem field="status" label="状态">
                    <ASelect v-model="updateForm.status" placeholder="请选择状态">
                        <AOption :value="1">启用</AOption>
                        <AOption :value="2">禁用</AOption>
                    </ASelect>
                </AFormItem>
            </AForm>
        </AModal>
    </div>
</template>

<script lang="tsx" setup>
import {users, storeUsers, updateUsers} from "@admin/api/user";
import {ref} from "vue";
import { Message } from '@arco-design/web-vue';
import {cloneDeep} from "lodash";

const columns = [
    {
        dataIndex: 'online_status_text',
        title: '在线状态'
    },
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
        align: 'center',
        width: 120
    },
    {
        dataIndex: 'following_count',
        title: '关注数',
        align: 'center',
        width: 120
    },
    {
        dataIndex: 'follower_count',
        title: '粉丝数',
        align: 'center',
        width: 120
    },
    {
        dataIndex: 'chat_group_count',
        title: '群组数',
        align: 'center',
        width: 120
    },
    {
        dataIndex: 'post_count',
        title: '动态数',
        align: 'center',
        width: 120
    },
    {
        dataIndex: 'created_at',
        title: '注册时间'
    }
];

const listDataRef = ref();

const createVisible = ref(false);

const updateVisible = ref(false);

const createFormRef = ref();

const updateFormRef = ref();

const createForm = ref({
    nickname: '',
    avatar: '',
    wallet_account: ''
})

const updateForm = ref({
    username: '',
    nickname: '',
    avatar: '',
    wallet_account: '',
    gender: '',
    location: '',
    birthday: '',
    self_signature: '',
    allow_type: '',
    language: 0,
    level: 0,
    role: 0,
    admin_forbid_type: '',
    status: ''
})

const fileList = ref([]);

const updateFileList = ref([]);

const renderData = async ({ current }) => {
    return await users({
        page: current
    })
}

const handleCreateUser = async (done) => {
    try {
        const validate = await createFormRef.value.validate();

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

const handleUpdate = async (record) => {
    updateForm.value = cloneDeep(record);
    if (record.avatar) {
        updateFileList.value = [{
            name: '',
            url: record.avatar
        }];
    }
    updateVisible.value = true;
}

const handleUpdateUser = async (done) => {
    try {
        const validate = await updateFormRef.value.validate();

        if (validate) {
            throw new Error(validate[Object.keys(validate)[0]].message || '请填写完整表单');
        }

        await updateUsers(updateForm.value.id, updateForm.value);
        done(true);
        Message.success('操作成功');
        updateFormRef.value.resetFields();
        updateFileList.value = [];
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

const handleUpdateFileUpload = (file) => {
    const response = file?.response;
    if (response) {
        updateForm.value.avatar = response.path;
    }
}
</script>
