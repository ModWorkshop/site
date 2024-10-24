<template>
    <m-form v-model="mod" :created="!!mod.id" float-save-gui :flush-changes="flushChanges" :exclude-from-compare="excludeFromCompare" @submit="save">
        <m-tabs padding="4" side query background>
            <m-tab name="main" :title="$t('main_tab')">
                <edit-mod-main v-model="mod"/>
            </m-tab>
            <m-tab name="downloads" :title="$t('downloads_tab')">
                <edit-mod-files v-model="mod"/>
            </m-tab>
            <m-tab name="images" :title="$t('images_tab')">
                <edit-mod-images v-model="mod"/>
            </m-tab>
            <m-tab name="members" :title="$t('members_tab')">
                <edit-mod-members v-model="mod"/>
            </m-tab>
            <m-tab name="instructions" :title="$t('instructions_tab')">
                <edit-mod-deps v-model="mod"/>
            </m-tab>
            <m-tab name="extra" :title="$t('extra_tab')">
                <edit-mod-extra v-model="mod"/>
            </m-tab>
        </m-tabs>
    </m-form>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import type { Mod } from '~~/types/models';
import { canEditMod, canSuperUpdate } from '~~/utils/mod-helpers';
import clone from 'rfdc/default';

const { setGame } = useStore();
const showErrorToast = useQuickErrorToast();
const flushChanges = createEventHook();
const store = useStore();

const initialMod = defineModel<Mod>('mod', { required: true });

if (initialMod.value.id == 0) {
    initialMod.value.user_id = store.user!.id;
    initialMod.value.user = store.user!;
}

const mod = ref<Mod>(clone(initialMod.value));

mod.value.send_for_approval ??= false;

if (!canEditMod(mod.value)) {
    useNoPermsPage();
}

provide('canSuperUpdate', canSuperUpdate(mod.value));
provide('mod', mod);

watch(() => mod.value.game, () => {
    if (mod.value.game) {
        setGame(mod.value.game);
    }
}, { immediate: true });

provide('flushChanges', flushChanges);

async function save() {
    try {
        let fetchedMod;
        if (!mod.value.id) {
            fetchedMod = await postRequest<Mod>(`/games/${mod.value.game_id}/mods`, mod.value);
            if (fetchedMod) {
                // router.push({ path: `/mod/${fetchedMod.id}/edit`, query: { tab: route.query.tab } });
                flushChanges.trigger(fetchedMod);
            }
        } else {
            fetchedMod = await patchRequest<Mod>(`mods/${mod.value.id}`, mod.value);
            if (fetchedMod) {
                flushChanges.trigger(mod.value);
            }
        }

        Object.assign(initialMod.value, fetchedMod);
    } catch (error) {
        showErrorToast(error);
        return;
    }
}

/**
 * Excludes things like 'download' that do not need to be tracked so the save float doesn't show up.
 */
 const excludeFromCompare = [
    'download',
    'thumbnail',
    'banner',
    'images',
    'files',
    'links',
    'transfer_request',
    'members',
    'has_download',
    'links_count',
    'files_count',
    'used_storage'
];
</script>