<template>
    <div @click="showModal = true">
        <slot>
            <a-button icon="flag" color="danger">{{$t('report')}}</a-button>
        </slot>
    </div>
    <Teleport to="body">
        <a-modal-form v-model="showModal" :title="$t('report')" @submit="report">
            <a-input v-model="reason" label="reason" type="textarea" rows="6"/>
        </a-modal-form>
    </Teleport>
</template>

<script setup lang="ts">
const props = defineProps({
    url: String
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