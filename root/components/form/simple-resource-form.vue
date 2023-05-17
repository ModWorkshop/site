<template>
    <a-form 
        :model="vm"
        :created="vm && !!vm.id"
        float-save-gui
        :can-save="canSave"
        :ignore-changes="ic"
        @submit="submit"
    >
        <flex column gap="3">
            <slot/>
            <a-alert v-if="deleteButton && vm.id" class="w-full" :title="$t('danger_zone')" color="danger">
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
import { EventRaiser } from '~~/composables/useEventRaiser';
import { serializeObject } from '~~/utils/helpers';
import { Game } from '../../types/models';

const router = useRouter();
const yesNoModal = useYesNoModal();
const showError = useQuickErrorToast();

const { t } = useI18n();

const props = withDefaults(defineProps<{
    modelValue: any,
    url: string,
    createUrl?: string,
    redirectTo?: string,
    deleteRedirectTo?: string,
    mergeParams?: object,
    canSave?: boolean,
    ignoreChanges?: EventRaiser,
    deleteButton?: boolean,
    game?: Game
}>(), { deleteButton: true });


const emit = defineEmits(['submit', 'update:modelValue']);
const vm = useVModel(props, 'modelValue', emit);
const ic = computed(() => props.ignoreChanges ?? useEventRaiser());
const createUrl = computed(() => props.createUrl ?? getGameResourceUrl(props.url, props.game));

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
            const model = await postRequest<{id: number}>(createUrl.value, params);
            if (props.redirectTo) {
                router.replace(`${props.redirectTo}/${model.id}`);
            }
        } else {
            vm.value = await patchRequest(`${props.url}/${vm.value.id}`, params);
            ic.value.execute();
        }

        emit('submit');
    } catch (error) {
        showError(error);
        return;
    }
}

async function doDelete() {
    yesNoModal({
        title: t('are_you_sure'),
        desc: t('irreversible_action'),
        async yes() {
            await deleteRequest(`${props.url}/${props.modelValue.id}`);
            const to = props.deleteRedirectTo ?? props.redirectTo;
            if (to) {
                router.replace(to);
            }
        }
    });
}
</script>

<style scoped>

</style>