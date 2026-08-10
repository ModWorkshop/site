<template>
	<m-flex grow class="overflow-hidden self-start w-full" style="min-height: 365px;">
		<m-tabs class="grow" query lazy scroll-on-overflow>
			<m-tab name="description" :title="$t('description')">
				<md-content allow-anchors :text="mod.desc" :parser-version="mod.parser_version"/>
			</m-tab>
			<m-tab v-if="mod.images && visibleImages.length > 0" name="images" :title="$t('images')">
				<div class="gallery">
					<m-img
						v-for="(image, i) of visibleImages"
						:key="image.id"
						loading="lazy"
						class="mod-image cursor-pointer"
						url-prefix="mods/images"
						:src="`${(image.has_thumb ? 'thumbnail_' : '') + image.file}`"
						@click="showImage(i)"
					/>
				</div>
				<vue-easy-lightbox move-disabled :visible="galleryVisible" :imgs="images" :index="imageIndex" @hide="galleryVisible = false"/>
			</m-tab>
			<m-tab v-if="mod.has_download" name="downloads" :title="$t('downloads')">
				<mod-downloads-tab :mod="mod"/>
			</m-tab>
			<m-tab v-if="mod.changelog" name="changelog" :title="$t('changelog')">
				<md-content allow-anchors :text="mod.changelog" :parser-version="mod.parser_version"/>
			</m-tab>
			<m-tab v-if="mod.license" name="license" :title="$t('license')">
				<md-content allow-anchors :text="mod.license" :parser-version="mod.parser_version"/>
			</m-tab>
			<m-tab v-if="dependencies.length || instructions" name="instructions" :title="$t('instructions_tab')" gap="3">
				<m-flex wrap>
					<div v-if="instructions" >
						<h2>{{ $t('instructions') }}</h2>
						<md-content class="mb-3" allow-anchors :text="instructions" :parser-version="mod.parser_version"/>
					</div>
					<div v-if="dependencies.length" class="flex-1">
						<h2>{{ $t('dependencies') }}</h2>
						<m-flex gap="1" column>
							<dependency-list-mod v-for="dep in dependencies" :key="dep.id" :dep="dep"/>
						</m-flex>
					</div>
				</m-flex>
			</m-tab>
		</m-tabs>
	</m-flex>
</template>

<script setup lang="ts">
import { useStore } from '~/store';
import type { Mod } from '~/types/models';

const props = defineProps<{
	mod: Mod;
}>();

const { public: config } = useRuntimeConfig();
const { hasPermission } = useStore();

const dependencies = computed(() => {
	const deps = props.mod.dependencies ?? [];
	const templateDeps = props.mod.instructs_template?.dependencies ?? [];

	const combinedDeps = [...deps, ...templateDeps];

	return combinedDeps.sort((a, b) => a.order === b.order ? a.id - b.id : a.order - b.order);
});

const instructions = computed(() => (props.mod.instructs_template ? props.mod.instructs_template.instructions + '\n\n' : '') + props.mod.instructions);

const imageIndex = ref(0);
const galleryVisible = ref(false);

function showImage(nextIndex) {
	imageIndex.value = nextIndex;
	galleryVisible.value = true;
}

const visibleImages = computed(() => props.mod.images?.filter(img => img.visible || hasPermission('manage-mods', props.mod.game)) || []);

const images = computed(() => {
	const images: string[] = [];

	for (const image of visibleImages.value) {
		images.push(`${config.storageUrl}/mods/images/${image.file}`);
	}

	return images;
});
</script>
