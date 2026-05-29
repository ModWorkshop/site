<template>
	<m-form v-model="userForm" float-save-gui autocomplete="off" @submit="save">
		<m-flex column gap="3">
			<m-select v-model="userForm.extra.default_mods_view" :options="viewOptions" :label="$t('default_view')"/>
			<m-select v-model="userForm.extra.default_mods_sort" :options="sortOptions" :label="$t('default_sorting')" default="bumped_at" clearable null-clear/>
			<h2>{{ $t('home_page') }}</h2>
			<m-select v-model="userForm.extra.home_default_mods_sort" :options="sortOptions" :label="$t('default_sorting')" default="bumped_at" clearable null-clear/>
			<m-input v-model="userForm.extra.home_show_last_games" :label="$t('show_last_updated')" type="checkbox"/>
			<m-input v-model="userForm.extra.home_show_mods" :label="$t('show_mods')" type="checkbox"/>
			<m-input v-model="userForm.extra.home_show_threads" :label="$t('show_threads')" type="checkbox"/>
			<h2>{{ $t('game_sections') }}</h2>
			<m-select v-model="userForm.extra.game_default_mods_sort" :options="sortOptions" :label="$t('default_sorting')" default="bumped_at" clearable null-clear/>
			<m-input v-model="userForm.extra.game_show_mods" :label="$t('show_mods')" type="checkbox"/>
			<m-input v-model="userForm.extra.game_show_threads" :label="$t('show_threads')" type="checkbox"/>
		</m-flex>
	</m-form>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~/store';
import type { User, UserForm } from '~/types/models';
import clone from 'rfdc/default';

const { user } = defineProps<{
	user: UserForm;
}>();

const userForm = reactive({
	extra: clone(user.extra)
});

const isMe = inject<boolean>('isMe');

if (!isMe) {
	useNoPermsPage();
}

const { t } = useI18n();

const showError = useQuickErrorToast();
const { setUser } = useStore();

const viewOptions = [
	{ id: 'followed', name: t('followed') },
	{ id: 'all', name: t('all') }
];

const sortOptions = [
	{ id: 'bumped_at', name: t('last_updated') },
	{ id: 'published_at', name: t('published_at') },
	{ id: 'score', name: t('popular_monthly') },
	{ id: 'daily_score', name: t('popular_today') },
	{ id: 'weekly_score', name: t('popular_weekly') },
	{ id: 'random', name: t('random') },
	{ id: 'downloads', name: t('downloads') },
	{ id: 'views', name: t('views') },
	{ id: 'name', name: t('name') },
	{ id: 'likes', name: t('likes') }
];

async function save() {
	try {
		setUser(await patchRequest<User>(`users/${user.id}`, userForm));
	} catch (error) {
		showError(error);
	}
}
</script>
