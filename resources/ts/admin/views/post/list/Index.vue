<template>
    <div>
        <Breadcrumb :items="['动态管理', '动态列表']"></Breadcrumb>
        <Panel title="动态列表">
            <div class="space-y-2 mb-4">
                <AForm ref="filterFormRef" :model="filterForm" :label-col-props="{ span: 6 }" :wrapper-col-props="{ span: 18 }" label-align="left">
                    <ARow :gutter="16">
                        <ACol :span="6">
                            <AFormItem field="user_ids" label="发布用户">
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
                    <Export :export-data="exportData" file-name="动态列表" />
                </template>
                <template #action="{record}">
                    <AButton @click="handleDelete(record)" type="text" size="small">删除</AButton>
                </template>
            </ListData>
        </Panel>
        <AImagePreviewGroup v-model:visible="imagePreviewVisible" :src-list="imageSrcList" infinite/>
    </div>
</template>

<script lang="tsx" setup>
import {posts, destroyPosts, exportPosts} from "@admin/api/post";
import {ref} from "vue";
import {Message, Modal} from '@arco-design/web-vue';
import UserSelect from "@admin/components/form/user-select/Index.vue";
import Export from "@admin/components/common/list-data/actions/export/Index.vue";

const columns = [
    {
        dataIndex: 'user',
        title: '发布用户',
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
        title: '内容'
    },
    {
        dataIndex: 'images',
        title: '图片',
        render: ({record}) => {
            return (
                <div>
                    {
                        record.images && record.images.length > 0 ?
                            <AButton type="text" onClick={() => handlePreviewImageList(record)}>
                                {{
                                    icon: <IconImage />
                                }}
                            </AButton> :
                            ''
                    }
                </div>
            )
        }
    },
    {
        dataIndex: 'visible_status_text',
        title: '可见状态'
    },
    {
        dataIndex: 'comment_count',
        title: '评论数',
        align: 'center'
    },
    {
        dataIndex: 'collect_count',
        title: '收藏数',
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

const exportData = async () => {
    return await exportPosts({
        ...filterForm.value
    })
}

const listDataRef = ref();

const renderData = async ({ current }) => {
    return await posts({
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

const handleDelete= (record) => {
    Modal.confirm({
        title: '确定要删除该动态？',
        content: '删除后该动态将不可见。',
        closable: true,
        onOk: async () => {
            await destroyPosts(record.id);
            Message.success('操作成功');
            listDataRef.value.refreshData();
        }
    });
}

const imagePreviewVisible = ref(false);

const imageSrcList = ref([]);

const handlePreviewImageList = (record) => {
    imageSrcList.value = record.images && record.images.map(item => item.url);
    imagePreviewVisible.value = true;
}
</script>
