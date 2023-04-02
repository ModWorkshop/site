<template>
    <div v-if="button" @click="vm = true">
        <slot>
            <a-button icon="mdi:flag" color="danger">{{$t('report')}}</a-button>
        </slot>
    </div>
    <Teleport to="body">
        <a-modal-form v-model="vm" :title="$t('report')" :desc="$t('report_desc', [$t(`resource_${resourceName}`)])" @submit="report">
            <a-input v-model="reason" :label="$t('reason')" type="textarea" rows="6"/>
        </a-modal-form>
    </Teleport>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
    url: string,
    resourceName: string,
    showModal?: boolean,
    button?: boolean,
}>(), {
    showModal: false,
    button: false
});

const emit = defineEmits(['update:showModal']);

const vm = useVModel(props, 'showModal', emit, { passive: true });

const reason = ref('');

async function report(onError) {
    try {
        await usePost(props.url, { reason: reason.value });
        reason.value = '';
        vm.value = false;
    } catch (error) {
        onError(error);
    }
}
</script>