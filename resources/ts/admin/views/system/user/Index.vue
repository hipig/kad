<template>
    <div>
        <Breadcrumb :items="['系统管理', '管理员列表']"></Breadcrumb>
        <Panel title="管理员列表">
            <div class="space-y-2 mb-4">
                <AForm ref="filterFormRef" :model="filterForm" :label-col-props="{ span: 6 }" :wrapper-col-props="{ span: 18 }" label-align="left">
                    <ARow :gutter="16">
                        <ACol :span="6">
                            <AFormItem field="username" label="用户名">
                                <AInput v-model="filterForm.username" allow-clear/>
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
                <template #action="{record}">
                    <AButton v-if="record.status === 1" @click="handleDisable(record)" type="text" size="small">禁用</AButton>
                    <AButton v-if="record.status === 2" @click="handleEnable(record)" type="text" size="small">启用</AButton>
                </template>
            </ListData>
        </Panel>
    </div>
</template>

<script lang="tsx" setup>
import {users, changeStatusUsers} from "@admin/api/admin-user";
import {ref} from "vue";
import {Message, Modal} from '@arco-design/web-vue';

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
        dataIndex: 'visible_status_text',
        title: '可见状态'
    },
    {
        dataIndex: 'view_count',
        title: '浏览数',
        align: 'center'
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
    username: ''
})

const listDataRef = ref();

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
</script>
