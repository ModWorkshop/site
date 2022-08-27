<template>
    <a-form v-if="modelValue" :model="modelValue" :created="modelValue && !!modelValue.id" float-save-gui :can-save="canSave" @submit="submit">
        <flex column gap="3">
            <slot/>
            <a-alert v-if="deleteButton && modelValue.id" class="w-full" color="warning">
                <details>
                    <summary class="uppercase">{{$t('danger_zone')}}</summary>
                    <div class="p-4 mt-2">
                        <a-button color="danger" @click="doDelete">{{$t('delete')}}</a-button>
                    </div>
                </details>
            </a-alert>
        </flex>
    </a-form>
</template>

<script setup lang="ts">
const router = useRouter();
const { init: showToast } = useToast();

const props = defineProps({
    modelValue: Object,
    url: String,
    redirectTo: String,
    mergeParams: Object,
    canSave: Boolean,
    deleteButton: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['submit', 'update:modelValue']);

async function submit() {
    try {
        let params;
        if (props.mergeParams) {
            params = serializeObject({
                ...props.modelValue,
                ...props.mergeParams
            });
        } else {
            params = props.modelValue;
        }

        if (!props.modelValue.id) {
            const model = await usePost<{id: number}>(props.url, params);
            router.replace({ path: `${props.redirectTo}/${model.id}` });
        } else {
            emit('update:modelValue', await usePatch(`${props.url}/${props.modelValue.id}`, params));
        }

        emit('submit');
    } catch (error) {
        console.error(error);
        showToast({ message: error.data.message, color: 'danger' });
        return;
    }
}

async function doDelete() {
    try {
        await useDelete(`${props.url}/${props.modelValue.id}`);
        router.replace({ path: props.redirectTo });
    } catch (error) {
        console.error(error);
        showToast({ message: error.data.message, color: 'danger' });
    }
}
</script>

<style scoped>

</style>