<template>
    <div>
        <Breadcrumb :items="['用户管理', '用户举报']"></Breadcrumb>
        <Panel title="用户举报">
            <div class="space-y-2 mb-4">
                <AForm ref="filterFormRef" :model="filterForm" :label-col-props="{ span: 6 }" :wrapper-col-props="{ span: 18 }" label-align="left">
                    <ARow :gutter="16">
                        <ACol :span="6">
                            <AFormItem field="user_ids" label="被举报用户">
                                <UserSelect v-model="filterForm.user_ids" multiple placeholder="请选择用户" />
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
                :selection-disabled-method="getDisabledSelection"
            >
                <template #actions="{selectionRowKeys}">
                    <AButton type="primary" :disabled="selectionRowKeys.length === 0" @click="handleHandle(selectionRowKeys)">批量处理</AButton>
                </template>
                <template #action="{record}">
                    <AButton v-if="!record.handled_at" @click="handleHandle([record.id])" type="text" size="small">处理</AButton>
                </template>
            </ListData>
        </Panel>
        <AModal :width="640" v-model:visible="handleVisible" title="处理举报" @before-ok="handleHandleConfirm">
            <AForm ref="handleFormRef" :model="handleForm" layout="vertical">
                <AFormItem field="handle_type" label="处理方式" :rules="[{required: true, message: '处理方式不能为空'}]">
                    <ARadioGroup v-model="handleForm.handle_type">
                        <ARadio value="NONE">不处理</ARadio>
                        <ARadio value="DISABLE_USER">禁用用户</ARadio>
                    </ARadioGroup>
                </AFormItem>
                <AFormItem field="handle_remark" label="处理备注" :rules="[{required: true, message: '处理备注不能为空'}]">
                    <ATextarea v-model="handleForm.handle_remark" placeholder="请输入" allow-clear/>
                </AFormItem>
            </AForm>
        </AModal>
    </div>
</template>

<script lang="tsx" setup>
import {reports, handleReports} from "@admin/api/report";
import {ref} from "vue";
import { Message } from '@arco-design/web-vue';
import UserSelect from "@admin/components/form/user-select/Index.vue";

const columns = [
    {
        dataIndex: 'nickname',
        title: '被举报用户',
        render: ({record}) => {
            return (
                <div class="flex items-center space-x-2">
                    <img width="24" src={record.reportable?.avatar} alt=""/>
                    <span>{record.reportable?.nickname}</span>
                </div>
            )
        }
    },
    {
        dataIndex: 'type',
        title: '举报类型'
    },
    {
        dataIndex: 'content',
        title: '举报内容'
    },
    {
        dataIndex: 'user.nickname',
        title: '举报人'
    },
    {
        dataIndex: 'handle_type_text',
        title: '处理方式'
    },
    {
        dataIndex: 'handle_remark',
        title: '处理备注'
    },
    {
        dataIndex: 'handled_at',
        title: '处理时间'
    }
];

const filterFormRef = ref();

const filterForm = ref({
    user_ids: []
})

const listDataRef = ref();

const handleVisible = ref(false);

const handleFormRef = ref();

const handleForm = ref({
    report_ids: [],
    handle_type: '',
    handle_remark: ''
})

const renderData = async ({ current }) => {
    return await reports({
        page: current,
        reportable_type: 'USER',
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

const handleHandle = (reportIds) => {
    handleForm.value.report_ids = reportIds;
    handleVisible.value = true;
}

const handleHandleConfirm = async (done) => {
    try {
        const validate = await handleFormRef.value.validate();

        if (validate) {
            throw new Error(validate[Object.keys(validate)[0]].message || '请填写完整表单');
        }

        await handleReports(handleForm.value);
        done(true);
        Message.success('操作成功');
        handleFormRef.value.resetFields();
        listDataRef.value.refreshData();
    } catch (e) {
        Message.error(e.message);
        done(false);
    }
}

const getDisabledSelection = (record) => {
    return record.handled_at;
}
</script>
