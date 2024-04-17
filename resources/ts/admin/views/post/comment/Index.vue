<template>
    <div>
        <Breadcrumb :items="['动态管理', '评论列表']"></Breadcrumb>
        <Panel title="评论列表">
            <div class="space-y-2 mb-4">
                <AForm ref="filterFormRef" :model="filterForm" :label-col-props="{ span: 6 }" :wrapper-col-props="{ span: 18 }" label-align="left">
                    <ARow :gutter="16">
                        <ACol :span="6">
                            <AFormItem field="user_ids" label="评论用户">
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
            >
                <template #actions="{selectionRowKeys}">
                    <Export :export-data="exportData" file-name="动态评论列表" />
                </template>
                <template #action="{record}">
                    <AButton @click="handleDelete(record)" type="text" size="small">删除</AButton>
                </template>
            </ListData>
        </Panel>
    </div>
</template>

<script lang="tsx" setup>
import {postComments, destroyComments, exportComments} from "@admin/api/post";
import {ref} from "vue";
import {Message, Modal} from '@arco-design/web-vue';
import UserSelect from "@admin/components/form/user-select/Index.vue";
import Export from "@admin/components/common/list-data/actions/export/Index.vue";

const columns = [
    {
        dataIndex: 'user',
        title: '评论用户',
        render: ({record}) => {
            return (
                <div class="flex items-center space-x-2">
                    <img width="24" src={record.user?.avatar} alt=""/>
                    <span>{record.user?.nickname}</span>
                </div>
            )
        }
    },
    {
        dataIndex: 'content',
        title: '评论内容'
    },
    {
        dataIndex: 'post.content',
        title: '动态内容'
    },
    {
        dataIndex: 'comment_count',
        title: '回复数',
        align: 'center'
    },
    {
        dataIndex: 'like_count',
        title: '点赞数',
        align: 'center'
    },
    {
        dataIndex: 'created_at',
        title: '创建时间'
    }
];

const filterFormRef = ref();

const filterForm = ref({
    user_ids: []
})

const listDataRef = ref();

const renderData = async ({ current }) => {
    return await postComments({
        page: current,
        ...filterForm.value
    })
}

const exportData = async () => {
    return await exportComments({
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

const handleDelete= (record) => {
    Modal.confirm({
        title: '确定要删除该评论？',
        content: '删除后该评论将不可见。',
        closable: true,
        onOk: async () => {
            await destroyComments(record.id);
            Message.success('操作成功');
            listDataRef.value.refreshData();
        }
    });
}
</script>
