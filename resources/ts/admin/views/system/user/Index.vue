<template>
    <div>
        <Breadcrumb :items="['系统管理', '管理员']"></Breadcrumb>
        <Panel title="管理员">
            <div class="space-y-2 mb-4">
                <AForm ref="filterFormRef" :model="filterForm" :label-col-props="{ span: 6 }" :wrapper-col-props="{ span: 18 }" label-align="left">
                    <ARow :gutter="16">
                        <ACol :span="6">
                            <AFormItem field="name" label="昵称">
                                <AInput v-model="filterForm.name" allow-clear/>
                            </AFormItem>
                        </ACol>
                        <ACol :span="6">
                            <AFormItem field="username" label="用户名">
                                <AInput v-model="filterForm.username" allow-clear/>
                            </AFormItem>
                        </ACol>
                        <ACol :span="6">
                            <AFormItem field="status" label="状态">
                                <ASelect v-model="filterForm.status" placeholder="请选择" allow-clear>
                                    <AOption :value="1">启用</AOption>
                                    <AOption :value="2">禁用</AOption>
                                </ASelect>
                            </AFormItem>
                        </ACol>
                    </ARow>
                </AForm>
                <div class="flex justify-end space-x-4">
                    <AButton @click="handleFilter" type="primary">
                        <template #icon>
                            <IconSearch/>
                        </template>
                        <span>查询</span>
                    </AButton>
                    <AButton @click="handleFilterReset">
                        <template #icon>
                            <IconRefresh/>
                        </template>
                        <span>重置</span>
                    </AButton>
                </div>
            </div>
            <ListData
                ref="listDataRef"
                :render-data="renderData"
                :columns="columns"
            >
                <template #actions>
                    <AButton @click="handleCreate" type="primary">添加管理员</AButton>
                </template>
                <template #action="{record}">
                    <AButton v-if="record.username !== 'admin'" @click="handleEdit(record)" type="text" size="small">编辑</AButton>
                    <AButton v-if="record.username !== 'admin' && record.status === 1" @click="handleDisable(record)" type="text" size="small">禁用</AButton>
                    <AButton v-if="record.username !== 'admin' && record.status === 2" @click="handleEnable(record)" type="text" size="small">启用</AButton>
                </template>
            </ListData>
        </Panel>
        <AModal :width="640" v-model:visible="createVisible" title="添加管理员" @before-ok="handleCreateUser">
            <AForm ref="createFormRef" :model="createForm" layout="vertical">
                <AFormItem field="username" label="用户名" :rules="[{required: true, message: '用户名不能为空'}]">
                    <AInput v-model="createForm.username" placeholder="请输入用户名" />
                </AFormItem>
                <AFormItem field="name" label="昵称" :rules="[{required: true, message: '昵称不能为空'}]">
                    <AInput v-model="createForm.name" placeholder="请输入昵称" />
                </AFormItem>
                <AFormItem field="password" label="密码" :rules="[{required: true, message: '密码不能为空'}]">
                    <AInput type="password" v-model="createForm.password" placeholder="请输入密码" />
                </AFormItem>
                <AFormItem field="password_confirmation" label="重复密码" :rules="[{required: true, message: '重复密码不能为空'}]">
                    <AInput type="password" v-model="createForm.password_confirmation" placeholder="请输入密码" />
                </AFormItem>
                <AFormItem field="status" label="状态">
                    <ASelect v-model="createForm.status" placeholder="请选择" allow-clear>
                        <AOption :value="1">启用</AOption>
                        <AOption :value="2">禁用</AOption>
                    </ASelect>
                </AFormItem>
            </AForm>
        </AModal>
        <AModal :width="640" v-model:visible="updateVisible" title="编辑管理员" @before-ok="handleUpdateUser">
            <AForm ref="updateFormRef" :model="updateForm" layout="vertical">
                <AFormItem field="username" label="用户名" :rules="[{required: true, message: '用户名不能为空'}]">
                    <AInput v-model="updateForm.username" placeholder="请输入用户名" />
                </AFormItem>
                <AFormItem field="name" label="昵称" :rules="[{required: true, message: '昵称不能为空'}]">
                    <AInput v-model="updateForm.name" placeholder="请输入昵称" />
                </AFormItem>
                <AFormItem field="password" label="密码" help="留空表示不修改密码">
                    <AInput type="password" v-model="updateForm.password" placeholder="请输入密码" />
                </AFormItem>
                <AFormItem field="password_confirmation" label="重复密码">
                    <AInput type="password" v-model="updateForm.password_confirmation" placeholder="请输入密码" />
                </AFormItem>
                <AFormItem field="role_ids" label="角色列表">
                    <ASelect v-model="updateForm.role_ids" :options="roleList" :field-names="{value: 'id', label: 'label'}" :loading="roleLoading" @search="handleRoleSearch" :filter-option="false" placeholder="请选择角色" multiple allow-clear />
                </AFormItem>
                <AFormItem field="status" label="状态">
                    <ASelect v-model="updateForm.status" placeholder="请选择" allow-clear>
                        <AOption :value="1">启用</AOption>
                        <AOption :value="2">禁用</AOption>
                    </ASelect>
                </AFormItem>
            </AForm>
        </AModal>
    </div>
</template>

<script lang="tsx" setup>
import {users, storeUsers, updateUsers, changeStatusUsers} from "@admin/api/admin-user";
import {roles} from "@admin/api/role";
import {onMounted, ref} from "vue";
import {Message, Modal} from '@arco-design/web-vue';
import {cloneDeep} from "lodash";

const columns = [
    {
        dataIndex: 'name',
        title: '昵称'
    },
    {
        dataIndex: 'username',
        title: '用户名'
    },
    {
        dataIndex: 'status_text',
        title: '状态'
    },
    {
        dataIndex: 'created_at',
        title: '创建时间'
    }
];

const filterFormRef = ref();

const filterForm = ref({
    name: '',
    username: '',
    status: ''
})

const roleLoading = ref(false);

const roleList = ref([]);

const listDataRef = ref();

const createVisible = ref(false);

const createFormRef = ref();

const createForm = ref({
    name: '',
    username: '',
    password: '',
    password_confirmation: '',
    status: 1
})

const updateVisible = ref(false);

const updateFormRef = ref();

const updateForm = ref({
    name: '',
    username: '',
    password: '',
    password_confirmation: '',
    role_ids: [],
    status: 1
})

const currentUserId = ref();

onMounted(async () => {
    await getRoleList();
})

const renderData = async ({ current }) => {
    return await users({
        page: current,
        ...filterForm.value
    })
}

const handleFilter = () => {
    listDataRef.value.refreshData();
}

const handleFilterReset = async () => {
    filterFormRef.value.resetFields();
    listDataRef.value.refreshData();
}

const getRoleList = async (keyword = '') => {
    const res = await roles({
        keyword,
        page_size: 20
    });
    roleList.value = res.data;
}
const handleRoleSearch = async (value) => {
    roleLoading.value = true;
    await getRoleList(value);
    roleLoading.value = false;
}

const handleDisable = (record) => {
    Modal.confirm({
        title: '禁用管理员',
        content: `禁用后管理员账户不可登录`,
        closable: true,
        onOk: async () => {
            await changeStatusUsers(record.id);
            Message.success('操作成功');
            listDataRef.value.refreshData();
        }
    });
}

const handleEnable = (record) => {

    Modal.confirm({
        title: '启用管理员',
        content: `启用后管理员账户可正常登录。`,
        closable: true,
        onOk: async () => {
            await changeStatusUsers(record.id);
            Message.success('操作成功');
            listDataRef.value.refreshData();
        }
    });
}

const handleCreate = () => {
    createFormRef.value.resetFields();
    createVisible.value = true;
}

const handleEdit = (record) => {
    updateFormRef.value.resetFields();
    currentUserId.value = record.id;
    updateForm.value = cloneDeep(record);
    updateForm.value.role_ids = record.roles.map(item => item.id);
    updateVisible.value = true;
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
        listDataRef.value.refreshData();
    } catch (e) {

        done(false);
    }
}

const handleUpdateUser = async (done) => {
    try {
        const validate = await updateFormRef.value.validate();

        if (validate) {
            throw new Error(validate[Object.keys(validate)[0]].message || '请填写完整表单');
        }

        await updateUsers(currentUserId.value, updateForm.value);

        done(true);
        Message.success('操作成功');
        updateFormRef.value.resetFields();
        listDataRef.value.refreshData();
    } catch (e) {

        done(false);
    }
}
</script>
