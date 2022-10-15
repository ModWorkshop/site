<template>
    <a-form 
        :model="modelValue"
        :created="modelValue && !!modelValue.id"
        float-save-gui
        :can-save="canSave"
        :ignore-changes="ignoreChanges"
        @submit="submit"
    >
        <flex column gap="3">
            <slot/>
            <a-alert v-if="deleteButton && modelValue.id" class="w-full" :title="$t('danger_zone')" color="danger">
                <slot name="danger-zone"/>
                <div>
                    <a-button color="danger" @click="doDelete">{{$t('delete')}}</a-button>
                </div>
            </a-alert>
        </flex>
    </a-form>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { serializeObject } from '~~/utils/helpers';

const router = useRouter();
const { showToast } = useToaster();
const yesNoModal = useYesNoModal();

const { t } = useI18n();

const props = defineProps({
    modelValue: Object,
    url: String,
    createUrl: String,
    redirectTo: String,
    deleteRedirectTo: String,
    mergeParams: Object,
    canSave: Boolean,
    ignoreChanges: Object,
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
            const model = await usePost<{id: number}>(props.createUrl ?? props.url, params);
            if (props.redirectTo) {
                router.replace(`${props.redirectTo}/${model.id}`);
            }
        } else {
            emit('update:modelValue', await usePatch(`${props.url}/${props.modelValue.id}`, params));
        }

        emit('submit');
    } catch (error) {
        console.error(error);
        showToast({ desc: error.data.message, color: 'danger' });
        return;
    }
}

async function doDelete() {
    yesNoModal({
        title: t('are_you_sure'),
        desc: t('irreversible_action'),
        async yes() {
            await useDelete(`${props.url}/${props.modelValue.id}`);
            router.replace(props.deleteRedirectTo ?? props.redirectTo);
        }
    });
}
</script>

<style scoped>

</style>