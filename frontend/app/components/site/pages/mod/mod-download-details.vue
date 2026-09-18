<template>
	<m-flex class="p-3" column gap="4">
		<m-flex gap="3" wrap>
			<m-flex v-if="file.version_type" gap="1" column class="flex-1">
				<span class="text-secondary">{{ $t('version_type') }}</span>
				<span class="info">{{ $t(`version_${file.version_type}`) }}</span>
			</m-flex>

			<m-flex v-if="file.version" gap="1" column class="flex-1">
				<span class="text-secondary">{{ $t('version') }}</span>
				<span class="info">{{ file.version }}</span>
			</m-flex>
		</m-flex>

		<m-flex gap="3" wrap>
			<m-flex v-if="file.size" gap="1" column class="flex-1">
				<span class="text-secondary">{{ $t('file_size') }}</span>
				<span class="info">{{ friendlySize(file.size) }}</span>
			</m-flex>

			<m-flex column class="flex-1">
				<span class="text-secondary" gap="1">{{ $t('downloads') }}</span>
				<span class="info">{{ friendlyNumber($i18n.locale, file.downloads) }}</span>
			</m-flex>
		</m-flex>

		<m-flex gap="3" wrap>
			<m-flex gap="1" column class="flex-1">
				<span class="text-secondary">{{ $t('name') }}</span>
				<m-flex class="items-center whitespace-pre-line info">
					<span v-if="file.name" class="items-center" style="word-break: break-word;">{{ file.name }}</span>
					<span v-else class="items-center">{{ $t(`file_type_${type}`) }}</span>
				</m-flex>
			</m-flex>
			<m-flex gap="1" column class="flex-1">
				<span class="text-secondary">{{ $t(type + '_id') }}</span>
				<span class="info">{{ file.id }}</span>
			</m-flex>
		</m-flex>
		<m-flex gap="3" wrap>
			<m-flex v-if="file.updated_at" gap="1" column class="flex-1">
				<span class="text-secondary">{{ $t('last_updated') }}</span>
				<m-time class="info" :datetime="file.updated_at" relative/>
			</m-flex>
			<m-flex v-if="file.created_at" gap="1" column class="flex-1">
				<span class="text-secondary">{{ $t('published_at') }}</span>
				<m-time v-if="!file.user" :datetime="file.created_at" relative class="info"/>
				<i18n-t v-else keypath="by_user_time_ago" scope="global" tag="span" class="info">
					<template #user>
						<a-user avatar-size="xs" :user="file.user" :tag="false" :avatar="false"/>
					</template>
					<template #time>
						<m-time :datetime="file.created_at" relative/>
					</template>
				</i18n-t>
			</m-flex>
		</m-flex>
		<m-flex v-if="file.desc" column>
			<span class="text-secondary">{{ $t('description') }}</span>
			<md-content :text="file.desc" :padding="1" style="max-height: 250px; overflow-y: auto;"/>
		</m-flex>
	</m-flex>
</template>

<script setup lang="ts">
import type { File, Link } from '~/types/models';

const { file } = defineProps<{
	file: File & Link;
	type: 'file' | 'link';
}>();
</script>

<style scoped>
.info {
	white-space: pre-line;
	word-break: break-words;
	overflow-wrap: anywhere;
	width: 60%;
}
</style>
