<template>
    <div>
        <Breadcrumb :items="['系统管理', '角色管理']"></Breadcrumb>
        <Panel title="角色管理">
            <div class="space-y-2 mb-4">
                <AForm ref="filterFormRef" :model="filterForm" :label-col-props="{ span: 6 }" :wrapper-col-props="{ span: 18 }" label-align="left">
                    <ARow :gutter="16">
                        <ACol :span="6">
                            <AFormItem field="label" label="名称">
                                <AInput v-model="filterForm.label" allow-clear/>
                            </AFormItem>
                        </ACol>
                        <ACol :span="6">
                            <AFormItem field="name" label="标识">
                                <AInput v-model="filterForm.name" allow-clear/>
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
                    <AButton @click="handleCreate" type="primary">添加角色</AButton>
                </template>
                <template #action="{record}">
                    <AButton @click="handleEdit(record)" type="text" size="small">编辑</AButton>
                    <AButton @click="handleAssign(record)" type="text" size="small">授权</AButton>
                </template>
            </ListData>
        </Panel>
        <AModal :width="640" v-model:visible="createVisible" :title="createTitle" @before-ok="handleCreateUser">
            <AForm ref="createFormRef" :model="createForm" layout="vertical">
                <AFormItem field="label" label="名称" :rules="[{required: true, message: '名称不能为空'}]">
                    <AInput v-model="createForm.label" placeholder="请输入名称" />
                </AFormItem>
                <AFormItem field="name" label="标识" :rules="[{required: true, message: '标识不能为空'}]">
                    <AInput v-model="createForm.name" placeholder="请输入标识" />
                </AFormItem>
            </AForm>
        </AModal>
        <AModal :width="640" v-model:visible="assignVisible" title="菜单授权" @before-ok="handleAssignConfirm">
            <AForm ref="assignFormRef" :model="assignForm" layout="vertical">
                <AFormItem field="menu_ids" label="菜单列表">
                    <ATree
                        v-model:checked-keys="assignForm.menu_ids"
                        :field-names="{
                          key: 'id',
                          title: 'name',
                          children: 'children',
                          icon: null
                        }"
                        :data="treeMenuData"
                        :check-strictly="false"
                        block-node
                        checkable />
                </AFormItem>
            </AForm>
        </AModal>
    </div>
</template>

<script lang="tsx" setup>
import {roles, storeRoles, updateRoles, assignMenuRoles, showRoles} from "@admin/api/role";
import {treeMenus} from "@admin/api/menu";
import {onMounted, ref} from "vue";
import {Message, Modal} from '@arco-design/web-vue';
import {cloneDeep} from "lodash";

const columns = [
    {
        dataIndex: 'label',
        title: '名称'
    },
    {
        dataIndex: 'name',
        title: '标识'
    },
    {
        dataIndex: 'created_at',
        title: '创建时间'
    }
];

const filterFormRef = ref();

const filterForm = ref({
    name: '',
    label: ''
})

const listDataRef = ref();

const treeMenuData = ref([]);

const createVisible = ref(false);

const createTitle = ref('');

const createFormRef = ref();

const createForm = ref({
    name: '',
    label: ''
})

const currentRoleId = ref();

const assignVisible = ref(false);

const assignFormRef = ref();

const assignForm = ref({
    menu_ids: []
})

onMounted(async () => {
    await getTreeMenuData();
})

const renderData = async ({ current }) => {
    return await roles({
        page: current,
        ...filterForm.value
    })
}

const getTreeMenuData = async () => {
    treeMenuData.value = await treeMenus();
}

const handleFilter = () => {
    listDataRef.value.refreshData();
}

const handleFilterReset = async () => {
    filterFormRef.value.resetFields();
    listDataRef.value.refreshData();
}

const handleCreate = () => {
    createFormRef.value.resetFields();
    createTitle.value = '添加角色';
    createVisible.value = true;
}

const handleEdit = async (record) => {
    createFormRef.value.resetFields();
    createTitle.value = '编辑角色';
    currentRoleId.value = record.id;
    createForm.value = cloneDeep(record);
    createVisible.value = true;
}

const handleCreateUser = async (done) => {
    try {
        const validate = await createFormRef.value.validate();

        if (validate) {
            throw new Error(validate[Object.keys(validate)[0]].message || '请填写完整表单');
        }

        if (currentRoleId.value) {
            await updateRoles(currentRoleId.value, createForm.value);
        } else {
            await storeRoles(createForm.value);
        }

        done(true);
        Message.success('操作成功');
        createFormRef.value.resetFields();
        listDataRef.value.refreshData();
    } catch (e) {
        done(false);
    }
}

const handleAssign = async (record) => {
    currentRoleId.value = record.id;
    assignForm.value.menu_ids = record.menus.map(item => item.id);
    assignVisible.value = true;
}

const handleAssignConfirm = async (done) => {
    try {
        const validate = await assignFormRef.value.validate();

        if (validate) {
            throw new Error(validate[Object.keys(validate)[0]].message || '请填写完整表单');
        }

        await assignMenuRoles(currentRoleId.value, assignForm.value);

        done(true);
        Message.success('操作成功');
        assignFormRef.value.resetFields();
        listDataRef.value.refreshData();
    } catch (e) {
        done(false);
    }
}
</script>
