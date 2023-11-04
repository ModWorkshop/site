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
                <flex gap="2">
                    <slot name="danger-zone"/>
                    <a-button color="danger" @click="doDelete"><i-mdi-delete/> {{$t('delete')}}</a-button>
                </flex>
            </a-alert>
            <NuxtTurnstile v-if="captcha" ref="turnstile" v-model="turnstileToken"/>
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

const turnstile = ref();
const turnstileToken = ref<string>();

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
    captcha?: boolean
}>(), { deleteButton: true });

const emit = defineEmits(['submit']);
const vm = defineModel<any>();
const fc = computed(() => props.flushChanges ?? createEventHook());
const createUrl = computed(() => props.createUrl ?? getGameResourceUrl(props.url, props.game));

async function submit() {
    const token = turnstileToken.value;
    turnstileToken.value = '';

    try {
        let params;
        if (props.mergeParams) {
            params = serializeObject({
                ...vm.value,
                ...props.mergeParams,
                'cf-turnstile-response': token
            });
        } else {
            params = vm.value;
            params ??= {};
            params['cf-turnstile-response'] = token;
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