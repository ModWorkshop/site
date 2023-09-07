<template>
    <a-form v-model="mod" :created="!!mod.id" float-save-gui :flush-changes="flushChanges" @submit="save" :transform-for-compare="excludeFromCompare">
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
import clone from 'rfdc/default';
import { useStore } from '~~/store';
import { Mod } from '~~/types/models';
import { canEditMod, canSuperUpdate } from '~~/utils/mod-helpers';

const { setGame } = useStore();
const showErrorToast = useQuickErrorToast();
const flushChanges = useEventRaiser();

definePageMeta({
    middleware: 'users-only',
});

const router = useRouter();

const mod = defineModel<Mod>('mod', { required: true });

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

/**
 * Only used in cases the changes were saved but AForm doesn't know about it
 */
function ignoreChanges() {
    Object.assign(mod, clone(mod));
}

provide('ignoreChanges', ignoreChanges);

async function save() {
    try {
        let fetchedMod;
        if (mod.value.id == -1) {
            fetchedMod = await postRequest<Mod>('mods', mod.value);
            if (fetchedMod) {
                router.replace({ path: `/mod/${mod.value.id}/edit` });
            }
        } else {
            fetchedMod = await patchRequest<Mod>(`mods/${mod.value.id}`, mod.value);
        }
        if (fetchedMod) {
            flushChanges.execute();
            mod.value = fetchedMod;
        }
    } catch (error) {
        showErrorToast(error);
        return;
    }
}

/**
 * Excludes things like 'download' that do not need to be tracked so the save float doesn't show up.
 */
function excludeFromCompare(A: Mod, B: Mod) {
    A.download = undefined;
    B.download = undefined;
    A.images = undefined;
    B.images = undefined;
    A.files = undefined;
    B.files = undefined;
    A.links = undefined;
    B.links = undefined;
}
</script>