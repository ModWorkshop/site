<template>
    <Teleport to="body">
        <m-form-modal v-model="vm" :title="$t('report')" :desc="$t('report_desc', [$t(`resource_${resourceName}`)])" @submit="report">
            <m-input v-model="reason" :label="$t('reason')" type="textarea" rows="6"/>
        </m-form-modal>
    </Teleport>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
    url: string,
    resourceName: string,
    showModal?: boolean
}>(), {
    showModal: false,
});

const emit = defineEmits(['update:showModal']);

const vm = useVModel(props, 'showModal', emit, { passive: true });

const reason = ref('');

const { t } = useI18n();
const { showToast } = useToaster();
async function report(onError) {
    try {
        await postRequest(props.url, { reason: reason.value });
        reason.value = '';
        vm.value = false;
        
        showToast({
            desc: t('report_sent'),
            color: 'success'
        });
    } catch (error) {
        onError(error);
    }
}
</script>