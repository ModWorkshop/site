<template>
<Teleport to="body">
    <Transition>
        <a-modal-form v-if="showModal" v-model="showModal" :title="approve ? $t('approve') : $t('reject')" @submit="submit">
            <a-input v-if="!approve" v-model="form.reason" :label="$t('reason')" type="textarea" rows="6"/>
            <a-input v-model="form.notify" :label="$t('notify_owner_members')" type="checkbox"/>
        </a-modal-form>
    </Transition>
</Teleport>
<flex>
    <div @click="showModal = true; approve = true;">
        <slot>
            <a-button>{{$t('approve')}}</a-button>
        </slot>
    </div>
    <div v-if="mod.approved === null" @click="showModal = true; approve = false;">
        <slot>
            <a-button>{{$t('reject')}}</a-button>
        </slot>
    </div>
</flex>
</template>

<script setup lang="ts">
import { Mod } from '~~/types/models';
import { remove } from '@vue/shared';

const props = defineProps<{
    mod: Mod,
    mods?: Mod[]
}>();

const emit = defineEmits<{
    ( e: 'approved', status: boolean ): void
}>();

const approve = ref(false);
const showModal = ref(false);
const form = reactive({
    status: approve,
    reason: '',
    notify: true
});

async function submit(onError) {
    try {
        await usePatch(`mods/${props.mod.id}/approved`, form);
        props.mod.approved = approve.value;
        form.reason = '';
        showModal.value = false;

        emit('approved', approve.value);

        if (props.mods) {
            remove(props.mods, props.mod);
        }
    } catch (error) {
        onError(error);
    }
}
</script>