<template>
<Teleport to="body">
    <Transition>
        <a-modal-form v-if="showModal" v-model="showModal" :title="approve ? $t('approve') : $t('reject')" save-button="" size="medium" @submit="submit">
            {{approve}}
            <a-input v-model="form.reason" label="reason" type="textarea" rows="6"/>
            <a-input v-model="form.notify" label="Notify owner and members" type="checkbox"/>
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

const props = defineProps<{
    mod: Mod,
}>();

const approve = ref(false);
const showModal = ref(false);
const form = reactive({
    status: approve,
    reason: '',
    notify: true
});

async function submit(ok, onError) {
    try {
        await usePatch(`mods/${props.mod.id}/approved`, form);
        props.mod.approved = approve.value;
        form.reason = '';
        ok();
    } catch (error) {
        onError(error);
    }
}
</script>