<template>
    <div>
        <Breadcrumb :items="['动态管理', '动态举报']"></Breadcrumb>
        <Panel title="动态举报">
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
import {Message} from "@arco-design/web-vue";

const columns = [
    {
        dataIndex: 'reportable.content',
        title: '被举报动态'
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
        dataIndex: 'created_at',
        title: '创建时间'
    },
    {
        dataIndex: 'handle_remark',
        title: '处理备注'
    }
];

const listDataRef = ref();

const handleVisible = ref(false);

const handleFormRef = ref();

const handleForm = ref({
    report_ids: [],
    handle_remark: ''
})

const renderData = async ({ current }) => {
    return await reports({
        page: current,
        reportable_type: 'POST'
    })
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
