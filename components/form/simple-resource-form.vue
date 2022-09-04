<template>
    <a-form v-if="modelValue" :model="modelValue" :created="modelValue && !!modelValue.id" float-save-gui :can-save="canSave" @submit="submit">
        <flex column gap="3">
            <slot/>
            <a-alert v-if="deleteButton && modelValue.id" class="w-full" :title="$t('danger_zone')" color="danger">
                <div>
                    <a-button color="danger" @click="doDelete">{{$t('delete')}}</a-button>
                </div>
            </a-alert>
        </flex>
    </a-form>
</template>

<script setup lang="ts">
const router = useRouter();
const { init: showToast } = useToast();
const yesNoModal = useYesNoModal();

const props = defineProps({
    modelValue: Object,
    url: String,
    redirectTo: String,
    deleteRedirectTo: String,
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
            router.replace(`${props.redirectTo}/${model.id}`);
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
    yesNoModal({
        title: 'Are you sure?',
        desc: 'This action is irreversible!',
        async yes() {
            await useDelete(`${props.url}/${props.modelValue.id}`);
            router.replace(props.deleteRedirectTo ?? props.redirectTo);
        }
    });
}
</script>

<style scoped>

</style>