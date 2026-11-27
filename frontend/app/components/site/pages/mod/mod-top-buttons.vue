<template>
	<m-flex v-if="canEdit || canManage" class="overflow-auto">
		<m-flex class="flex-shrink-0">
			<NuxtLink v-if="$route.name == 'mod-mod-edit' || $route.name == 'upload' || $route.name == 'g-game-upload'" :to="`/mod/${mod.id}`">
				<m-button><i-mdi-arrow-left/> {{ $t('return_to_mod') }}</m-button>
			</NuxtLink>
			<m-button v-else-if="canEdit" :to="`/mod/${mod.id}/edit`"><i-mdi-cog/> {{ $t('edit_mod') }}</m-button>
			<mod-suspend v-model:show-modal="showSuspension" :button="false" :mod="mod"/>
			<m-dropdown v-if="canManage">
				<m-button><i-mdi-gavel/> {{ $t('moderation') }} <i-mdi-chevron-down/></m-button>
				<template #content>
					<m-dropdown-item @click="showSuspension = true">{{ mod.suspended ? $t('unsuspend') : $t('suspend') }}</m-dropdown-item>
					<m-dropdown-item v-if="mod.images?.length" @click="deleteAllImages">{{ $t('delete_images') }}</m-dropdown-item>
					<m-dropdown-item v-if="mod.files?.data.length" @click="deleteAllFiles">{{ $t('delete_files') }}</m-dropdown-item>
					<div class="dropdown-splitter"/>
					<m-dropdown-item v-if="!mod.highlighted" @click="showHighlightMod = true">Highlight Mod</m-dropdown-item>
					<m-dropdown-item v-else @click="UnhighlightMod">Unhighlight Mod</m-dropdown-item>
				</template>
			</m-dropdown>

		<m-form-modal v-model="showHighlightMod" title="Highlight Mod" @submit="highlgihtMod">
			<m-input v-model="highlightExpireDate" label="Expires At" type="date"/>
		</m-form-modal>
		</m-flex>
	</m-flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~/store';
import type { Mod } from '~/types/models';
import { Paginator } from '~/types/paginator';

const { mod } = defineProps<{
	mod: Mod;
}>();

const showSuspension = ref(false);
const showHighlightMod = ref(false);
const highlightExpireDate = ref(Date.now());

const { hasPermission } = useStore();
const yesNoModal = useYesNoModal();
const showErrorModal = useQuickErrorToast();
const { t } = useI18n();
const canEdit = computed(() => canEditMod(mod));
const canManage = computed(() => hasPermission('manage-mods', mod.game));

async function highlgihtMod() {
	try {
		await postRequest(`mods/${mod.id}/highlight`, {
			expires_at: highlightExpireDate.value
		});

		mod.highlighted = true;
		showHighlightMod.value = false;
	} catch (error) {
		showErrorModal(error);
	}
}

async function UnhighlightMod() {
	try {
		await deleteRequest(`mods/${mod.id}/highlight`);

		mod.highlighted = false;
	} catch (error) {
		showErrorModal(error);
	}
}

function deleteAllFiles() {
	yesNoModal({
		desc: t('delete_files_desc'),
		descType: 'warning',
		async yes() {
			await deleteRequest(`mods/${mod.id}/files`);
			mod.files = new Paginator();
			if (mod.download_type === 'file') {
				mod.download = undefined;
				mod.download_type = undefined;
			}
			mod.has_download = mod.links ? mod.links.data.length > 0 : false;
		}
	});
}

function deleteAllImages() {
	yesNoModal({
		desc: t('delete_images_desc'),
		descType: 'warning',
		async yes() {
			await deleteRequest(`mods/${mod.id}/images`);
			mod.images = [];
			mod.thumbnail = undefined;
			mod.thumbnail_id = undefined;
			mod.banner = undefined;
			mod.banner_id = undefined;
		}
	});
}
</script>
