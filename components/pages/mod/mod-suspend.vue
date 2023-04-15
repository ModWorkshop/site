<template>
<Teleport to="body">
    <Transition>
        <a-modal-form v-if="vm" v-model="vm" :title="mod.suspended ? $t('unsuspend') : $t('suspend')" @submit="suspend">
            <a-input v-model="suspendForm.reason" :label="$t('reason')" type="textarea" rows="6" minlength="3"/>
            <a-input v-model="suspendForm.notify" :label="$t('notify_owner_members')" type="checkbox"/>
        </a-modal-form>
    </Transition>
</Teleport>
<div v-if="button" @click="vm = true">
    <slot>
        <a-button>{{$t(mod.suspended ? 'unsuspend' : 'suspend')}}</a-button>
    </slot>
</div>
</template>

<script setup lang="ts">
import { Mod, Suspension } from '~~/types/models';

const props = withDefaults(defineProps<{
    mod: Mod,
    suspension?: Suspension,
    showModal?: boolean,
    button: boolean
}>(), {
    showModal: false,
    button: true
});

const emit = defineEmits(['update:showModal']);
const vm = useVModel(props, 'showModal', emit, { passive: true });

const suspendForm = reactive({
    status: computed(() => !props.mod.suspended),
    reason: '',
    notify: true
});

async function suspend(onError) {
    try {
        const suspension = await usePatch<Suspension>(`mods/${props.mod.id}/suspended`, suspendForm);

        props.mod.suspended = !props.mod.suspended;
        props.mod.last_suspension = suspension;
        suspendForm.reason = '';
        vm.value = false;

        if (props.suspension) {
            props.suspension.status = false;
        }
    } catch (error) {
        onError(error);
    }
}
</script>