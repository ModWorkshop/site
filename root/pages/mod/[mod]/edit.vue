<template>
    <page-block size="md" :game="mod.game" :breadcrumb="breadcrumb">
        <flex>
            <NuxtLink v-if="mod.id" :to="`/mod/${mod.id}`">
                <a-button icon="arrow-left">{{$t('return_to_mod')}}</a-button>
            </NuxtLink> 
        </flex>
        <a-alert v-if="!mod.published_at && mod.visibility == 'public' && mod.has_download" color="warning">
            {{$t('publish_mod_desc')}}
            <a-button class="mr-auto" icon="mdi:upload" @click="publish">{{ $t('publish_mod') }}</a-button>
        </a-alert>
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
    </page-block>
</template>

<script setup lang="ts">
import clone from 'rfdc/default';
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Mod } from '~~/types/models';
import { Paginator } from '~~/types/paginator';
import { canEditMod, canSuperUpdate } from '~~/utils/mod-helpers';

const { user, setGame } = useStore();
const { showToast } = useToaster();
const showErrorToast = useQuickErrorToast();

const { t } = useI18n();

definePageMeta({
    middleware: 'users-only',
});

const router = useRouter();

const { data: mod } = await useEditResource<Mod>('mod', 'mods', {
    id: -1,
    name: '',
    desc: '',
    images: [],
    files: new Paginator(),
    links: new Paginator(),
    members: [],
    short_desc: '',
    changelog: '',
    license: '',
    instructions: '',
    donation: '',
    legacy_banner_url: '',
    game_id: -1,
    version: '',
    user_id: user!.id,
    user: user!,
    downloads: 0,
    likes: 0,
    views: 0,
    visibility: 'public',
    suspended: false,
    comments_disabled: false,
    has_download: false,
    approved: false,
});

const breadcrumb = computed(() => {
    return [
        { name: t('games'), to: 'games' },
        ...(mod.value.breadcrumb ? mod.value.breadcrumb : []),
        { name: t('edit') }
    ];
});

mod.value.send_for_approval ??= false;

if (!canEditMod(mod.value)) {
    useNoPermsPage();
}

provide('canSuperUpdate', canSuperUpdate(mod.value));
provide('mod', mod.value);

watch(() => mod.value.game, () => {
    if (mod.value.game) {
        setGame(mod.value.game);
    }
}, { immediate: true });

async function publish() {
    try {
        mod.value = await patchRequest<Mod>(`mods/${mod.value.id}`, { publish: true, ...mod.value });
    } catch (error) {
        showErrorToast(error);
    }
}

/**
 * Only used in cases the changes were saved but AForm doesn't know about it
 */
function ignoreChanges() {
    mod.value = clone(mod.value);
}

provide('ignoreChanges', ignoreChanges);

function catchError(error) {
    showToast({ desc: error.data.message, color: 'danger' });
}

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
            mod.value = fetchedMod;
        }
    } catch (error) {
        catchError(error);
        return;
    }
}

</script>