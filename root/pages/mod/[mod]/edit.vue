<template>
    <a-form :model="mod" :created="!!mod.id" float-save-gui @submit="save">
        <content-block :padding="false" class="max-md:p-4 p-8">
            <a-tabs padding="4" side query>
                <a-tab name="main" :title="$t('main_tab')">
                    <edit-mod-main :mod="mod"/>
                </a-tab>
                <a-tab name="downloads" :title="$t('downloads_tab')">
                    <edit-mod-files :mod="mod"/>
                </a-tab>
                <a-tab name="images" :title="$t('images_tab')">
                    <edit-mod-images :mod="mod"/>
                </a-tab>
                <a-tab name="members" :title="$t('members_tab')">
                    <edit-mod-members :mod="mod"/>
                </a-tab>
                <a-tab name="instructions" :title="$t('instructions_tab')">
                    <edit-mod-deps :mod="mod"/>
                </a-tab>
                <a-tab name="extra" :title="$t('extra_tab')">
                    <edit-mod-extra :mod="mod"/>
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

definePageMeta({
    middleware: 'users-only',
});

const router = useRouter();

const { mod } = defineProps<{
    mod: Mod;
}>();

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
        if (mod.id == -1) {
            fetchedMod = await postRequest<Mod>('mods', mod);
            if (fetchedMod) {
                router.replace({ path: `/mod/${mod.id}/edit` });
            }
        } else {
            fetchedMod = await patchRequest<Mod>(`mods/${mod.id}`, mod);
        }
        if (fetchedMod) {
            Object.assign(mod, fetchedMod);
        }
    } catch (error) {
        showErrorToast(error);
        return;
    }
}

</script>