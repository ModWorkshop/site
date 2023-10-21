<template>
    <a-form v-model="mod" :created="!!mod.id" float-save-gui :flush-changes="flushChanges" :exclude-from-compare="excludeFromCompare" @submit="save">
        <content-block :padding="false" class="max-md:p-4 p-8">
            <a-tabs padding="4" side query>
                <a-tab name="main" :title="$t('main_tab')">
                    <edit-mod-main v-model="mod"/>
                </a-tab>
                <a-tab name="downloads" :title="$t('downloads_tab')">
                    <edit-mod-files v-model="mod"/>
                </a-tab>
                <a-tab name="images" :title="$t('images_tab')">
                    <edit-mod-images v-model="mod"/>
                </a-tab>
                <a-tab name="members" :title="$t('members_tab')">
                    <edit-mod-members v-model="mod"/>
                </a-tab>
                <a-tab name="instructions" :title="$t('instructions_tab')">
                    <edit-mod-deps v-model="mod"/>
                </a-tab>
                <a-tab name="extra" :title="$t('extra_tab')">
                    <edit-mod-extra v-model="mod"/>
                </a-tab>
            </a-tabs>
        </content-block>
    </a-form>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Mod } from '~~/types/models';
import { canEditMod, canSuperUpdate } from '~~/utils/mod-helpers';
import clone from 'rfdc/default';

const { setGame } = useStore();
const showErrorToast = useQuickErrorToast();
const flushChanges = createEventHook();

definePageMeta({
    middleware: 'users-only',
});

const initialMod = defineModel<Mod>('mod', { required: true });
const mod = reactive(clone(initialMod.value));

const router = useRouter();

mod.send_for_approval ??= false;

if (!canEditMod(mod)) {
    useNoPermsPage();
}

provide('canSuperUpdate', canSuperUpdate(mod));
provide('mod', mod);

watch(() => mod.game, () => {
    if (mod.game) {
        setGame(mod.game);
    }
}, { immediate: true });

provide('flushChanges', flushChanges);

async function save() {
    try {
        let fetchedMod;
        if (mod.id == -1) {
            fetchedMod = await postRequest<Mod>('mods', mod);
            if (fetchedMod) {
                router.replace({ path: `/mod/${mod.id}/edit` });
            }
        } else {
            fetchedMod = await patchRequest<Mod>(`mods/${mod.id}`, mod);
        }
        if (fetchedMod) {
            flushChanges.trigger(mod);
            initialMod.value = mod;
        }
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
    'files_count'
];
</script>