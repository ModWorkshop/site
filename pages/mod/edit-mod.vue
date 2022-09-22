<template>
    <page-block size="md">
        <flex>
            <NuxtLink v-if="mod.id" :to="`/mod/${mod.id}`">
                <a-button icon="arrow-left">{{$t('return_to_mod')}}</a-button>
            </NuxtLink> 
        </flex>
        <a-form :model="mod" :created="!!mod.id" :save-text="saveText" float-save-gui @submit="save" @state-changed="formStateChanged">
            <content-block class="p-8">
                <a-tabs padding="4" side query>
                    <a-tab name="main" title="Main">
                        <edit-mod-main :mod="mod"/>
                    </a-tab>
                    <a-tab name="files" title="Downloads & Updates">
                        <edit-mod-files :mod="mod" :can-save="canSave"/>
                    </a-tab>
                    <a-tab name="images" title="Images">
                        <edit-mod-images :mod="mod"/>
                    </a-tab>
                    <a-tab name="members" title="Members">
                        <edit-mod-members :mod="mod"/>
                    </a-tab>
                    <a-tab name="instructions" title="Dependencies & Instructions">
                        <edit-mod-deps :mod="mod"/>
                    </a-tab>
                    <a-tab name="extra" title="Extra">
                        <edit-mod-extra :mod="mod"/>
                    </a-tab>
                </a-tabs>
            </content-block>
        </a-form>
    </page-block>
</template>

<script setup lang="ts">
import clone from 'rfdc/default';
import { useStore } from '~~/store';
import { Mod } from '~~/types/models';

const { user, setGame } = useStore();
const { showToast } = useToaster();

definePageMeta({
    middleware: 'users-only',
});

const router = useRouter();

const canSave = ref(false);

const { data: mod } = await useEditResource<Mod>('mod', 'mods', {
    id: -1,
    name: '',
    desc: '',
    images: [],
    files: [],
    links: [],
    members: [],
    short_desc: '',
    changelog: '',
    license: '',
    instructions: '',
    donation: '',
    legacy_banner_url: '',
    game_id: -1,
    version: '',
    user_id: user.id,
    user,
    downloads: 0,
    likes: 0,
    views: 0,
    visibility: 1,
    suspended: false,
    comments_disabled: false,
    has_download: false,
    approved: false,
});

mod.value.send_for_approval ??= false;

if (!canEditMod(mod.value)) {
    showError("You don't have permission to view this page");
}

provide('canSuperUpdate', canSuperUpdate(mod.value));
provide('canSave', canSave);
provide('mod', mod.value);

watch(() => mod.value.game, () => {
    setGame(mod.value.game);
}, { immediate: true });

/**
 * Only used in cases the changes were saved but AForm doesn't know about it
 */
function ignoreChanges() {
    if (!canSave.value) {
        mod.value = clone(mod.value);
    }
}

provide('ignoreChanges', ignoreChanges);

const saveText = computed(() => mod.value.id == -1 ? 'Your mod is not uploaded yet' : 'You have unsaved changes');
function catchError(error) {
    showToast({ desc: error.data.message, color: 'danger' });
}

async function save() {
    try {
        let fetchedMod;
        if (mod.value.id == -1) {
            fetchedMod = await usePost<Mod>('mods', mod.value);
            if (fetchedMod) {
                router.replace({ path: `/mod/${mod.value.id}/edit` });
            }
        } else {
            fetchedMod = await usePatch<Mod>(`mods/${mod.value.id}`, mod.value);
        }
        if (fetchedMod) {
            mod.value = fetchedMod;
        }
    } catch (error) {
        catchError(error);
        return;
    }
}

function formStateChanged(canSaveState: boolean) {
    canSave.value = canSaveState;
}

</script>