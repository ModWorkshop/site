<template>
    <page-block v-if="mod" size="med">
        <flex>
            <nuxt-link v-if="mod.id" :to="`/mod/${mod.id}`">
                <a-button icon="arrow-left">{{$t('return_to_mod')}}</a-button>
            </nuxt-link> 
        </flex>
        <a-form :model="mod" :created="mod.id != -1" :save-text="saveText" float-save-gui @submit="save" @state-changed="formStateChanged">
            <content-block class="p-8">
                <a-tabs padding="4" side>
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
                    <a-tab name="instructions" title="Instructions"/>
                </a-tabs>
            </content-block>
        </a-form>
    </page-block>
</template>

<script setup lang="ts">
import clone from 'rfdc/default';
import { Ref } from 'vue';
import { useStore } from '~~/store';
import { Mod } from '~~/types/models';
import canEditMod from '~~/utils/mod-helpers';

const { hasPermission, user } = useStore();
const { init } = useToast();

definePageMeta({
    middleware: 'users-only',
});

const route = useRoute();

const modTemplate = {
    id: -1,
    name: '',
    desc: '',
    images: [],
    files: [],
    links: [],
    short_desc: '',
    game_id: null,
    category_id: null,
    tag_ids: [],
    version: '',
    user: user,
    nsfwMod: false,
    download_id: null,
    download_type: null,
    visibility: 1
};

const mod = ref<Mod>(clone(modTemplate));
const canSave = ref(false);

if (route.params.id) {
    const { data: fetchedMod } = await useFetchData<Mod>(`mods/${route.params.id}/`);
    mod.value = fetchedMod.value;
    // mod.tag_ids = mod.tags.map(tag => tag.id);

    if (!canEditMod(mod.value)) {
        throw throwError("You don't have permission to view this page");
    }
}

provide('canSuperUpdate', canSuperUpdate(mod.value));
provide('canSave', canSave);
provide('mod', mod.value);

/**
 * Only used in cases the changes were saved but AForm doesn't know about it
 */
function ignoreChanges() {
    mod.value = clone(mod.value);
}

provide('ignoreChanges', ignoreChanges);

const saveText = computed(() => mod.value.id == -1 ? 'Your mod is not uploaded yet' : 'You have unsaved changes');
function catchError(error) {
    init({ message: error.data.message, color: 'danger' });
}

async function save() {
    try {
        let fetchedMod;
        if (mod.value.id == -1) {
            fetchedMod = await usePost<Mod>('mods', mod.value).catch(catchError);
            if (fetchedMod) {
                history.replaceState(null, null, `/mod/${mod.value.id}/edit`);
            }
        } else {
            fetchedMod = await usePatch<Mod>(`mods/${mod.value.id}`, mod.value).catch(catchError);
        }
        if (fetchedMod) {
            mod.value = fetchedMod;
        }
    } catch (error) {
        console.error(error);
        return;
    }
}

function formStateChanged(canSaveState: boolean) {
    canSave.value = canSaveState;
}
</script>