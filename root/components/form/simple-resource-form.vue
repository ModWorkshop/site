<template>
    <a-form 
        v-model="vm"
        :created="vm && !!vm.id"
        float-save-gui
        :can-save="canSave"
        :flush-changes="fc"
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
import { serializeObject } from '~~/utils/helpers';
import { Game } from '../../types/models';
import { EventHook } from '@vueuse/core';

const router = useRouter();
const yesNoModal = useYesNoModal();
const showError = useQuickErrorToast();

const { t } = useI18n();

const props = withDefaults(defineProps<{
    url: string,
    createUrl?: string,
    redirectTo?: string,
    deleteRedirectTo?: string,
    mergeParams?: object,
    canSave?: boolean,
    flushChanges?: EventHook,
    deleteButton?: boolean,
    game?: Game,
}>(), { deleteButton: true });

const emit = defineEmits(['submit']);
const vm = defineModel<any>();
const fc = computed(() => props.flushChanges ?? createEventHook());
const createUrl = computed(() => props.createUrl ?? getGameResourceUrl(props.url, props.game));

async function submit() {
    try {
        let params;
        if (props.mergeParams) {
            params = serializeObject({
                ...vm.value,
                ...props.mergeParams
            });
        } else {
            params = vm.value;
        }

        if (!vm.value.id) {
            const model = await postRequest<{id: number}>(createUrl.value, params);
            if (props.redirectTo) {
                router.replace(`${props.redirectTo}/${model.id}`);
            }
        } else {
            fc.value.trigger(await patchRequest(`${props.url}/${vm.value.id}`, params));
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
            await deleteRequest(`${props.url}/${vm.value.id}`);
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