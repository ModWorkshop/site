<template>
<Teleport to="body">
    <Transition>
        <a-modal-form v-if="showSuspendModal" v-model="showSuspendModal" :title="mod.suspended ? $t('unsuspend') : $t('suspend')" @submit="suspend">
            <a-input v-model="suspendForm.reason" :label="$t('reason')" type="textarea" rows="6" minlength="3"/>
            <a-input v-model="suspendForm.notify" :label="$t('notify_owner_members')" type="checkbox"/>
        </a-modal-form>
    </Transition>
</Teleport>
<div @click="showSuspendModal = true">
    <slot>
        <a-button>{{$t(mod.suspended ? 'unsuspend' : 'suspend')}}</a-button>
    </slot>
</div>
</template>

<script setup lang="ts">
import { Mod, Suspension } from '~~/types/models';

const props = defineProps<{
    mod: Mod,
    suspension?: Suspension,
}>();

const showSuspendModal = ref(false);
const suspendForm = reactive({
    status: computed(() => !props.mod.suspended),
    reason: '',
    notify: true
});

async function suspend(onError) {
    try {
        await usePatch(`mods/${props.mod.id}/suspended`, suspendForm);

        props.mod.suspended = !props.mod.suspended;
        suspendForm.reason = '';
        showSuspendModal.value = false;

        if (props.suspension) {
            props.suspension.status = false;
        }
    } catch (error) {
        onError(error);
    }
}
</script>