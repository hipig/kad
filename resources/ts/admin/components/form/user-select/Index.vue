<template>
    <ASelect v-model="_value" :options="userOptions" :loading="userLoading" @search="handleUserSearch" @change="handleChange" :filter-option="false" allow-clear />
</template>

<script lang="ts" setup>
import {ref, watch, computed, onMounted} from "vue";
import {users} from "@admin/api/user";

const props = defineProps({
    modelValue: [Array]
});
const emit = defineEmits(['update:modelValue']);

const _value = ref();

watch(
    () => props.modelValue,
    (n) => {
        _value.value = n;
    },
    {deep: true}
)

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

const handleChange = (value) => {
    emit('update:modelValue', value);
}
</script>
