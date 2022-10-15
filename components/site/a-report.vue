<template>
    <div @click="showModal = true">
        <slot>
            <a-button icon="flag" color="danger">{{$t('report')}}</a-button>
        </slot>
    </div>
    <Teleport to="body">
        <a-modal-form v-model="showModal" :title="$t('report')" :desc="$t('report_desc', [$t(`resource_${resourceName}`)])" @submit="report">
            <a-input v-model="reason" :label="$t('reason')" type="textarea" rows="6"/>
        </a-modal-form>
    </Teleport>
</template>

<script setup lang="ts">
const props = defineProps({
    url: String,
    resourceName: String
});

const showModal = ref(false);
const reason = ref('');

async function report(ok, onError) {
    try {
        await usePost(props.url, { reason: reason.value });
        reason.value = '';
        ok();
    } catch (error) {
        onError(error);
    }
}
</script>