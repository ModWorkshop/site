<template>
	<simple-resource-form v-if="role" v-model="role" :url="url" :redirect-to="adminUrl" :delete-button="role.id !== 1 || !!gameId">
		<Title>
			{{ role.name }}
		</Title>
		<m-alert v-if="role.id == 1 && !gameId" color="warning">
			<span>{{ $t('members_role_desc') }}</span>
		</m-alert>
		<m-alert v-if="role.id && role.is_vanity" :title="`${$t('vanity_role')}✨`" :desc="$t('vanity_role_desc')"/>
		<m-input v-model="role.name" :label="$t('name')" maxlength="30" minlength="2"/>
		<m-input v-model="role.tag" :label="$t('user_tag')" :desc="$t('user_tag_help')" maxlength="30" minlength="2"/>
		<m-input v-model="role.color" :label="$t('color')" type="color"/>
		<m-input v-if="!role.id" v-model="role.is_vanity" type="checkbox" :label="$t('vanity_role')" :desc="$t('vanity_role_desc')"/>
		<m-input v-if="role.is_vanity" v-model="role.self_assignable" type="checkbox" :label="$t('self_assignable_role')" :desc="$t('self_assignable_role_desc')"/>
		<m-input v-if="!role.is_vanity" :label="$t('permissions')">
			<m-flex style="background-color: var(--alt-content-bg-color)" column grow>
				<m-flex
					v-for="perm of validPerms"
					:key="perm.id"
					class="perm items-center p-2"
					:title="hasPermission(perm.name, game) ? undefined : $t('cant_grant_permission')"
				>
					<m-input
						v-model="role.permissions![perm.name]"
						class="p-3"
						:label="perm.name"
						type="checkbox"
						:disabled="!hasPermission(perm.name, game)"
						@click="togglePermission(perm.name)"
					/>
					<span v-if="!hasPermission(perm.name, game)">🔒</span>
				</m-flex>
			</m-flex>
		</m-input>
	</simple-resource-form>
</template>

<script setup lang="ts">
import { useStore } from '~/store';
import type { Game, Permission, Role } from '~/types/models';

const props = defineProps<{
	game: Game;
}>();

useNeedsPermission('manage-roles', props.game);

const route = useRoute();
const { hasPermission } = useStore();

const gameId = computed(() => route.params.game);
const url = computed(() => gameId.value ? `games/${gameId.value}/roles` : 'roles');
const adminUrl = computed(() => getAdminUrl('roles', props.game));

const { data: role } = await useEditResource<Role>('role', url.value, {
	id: 0,
	name: '',
	tag: '',
	desc: '',
	order: 1,
	is_vanity: false,
	self_assignable: true,
	permissions: {}
});

const { data: roles } = await useFetchMany<Role>('roles', { params: { with_permissions: 1 } });

const member = computed(() => roles.value?.data[0]);

const { data: permissions } = await useFetchMany<Permission>('/permissions');

const validPerms = computed(() => {
	if (!gameId.value && role.value.id === 1) {
		return permissions.value?.data;
	} else {
		return permissions.value?.data.filter(perm => {
			const useless = member.value?.permissions?.[perm.name];
			if (useless) { // Everyone has this permission already!
				return false;
			}

			if (perm.type) {
				return gameId.value ? perm.type === 'game' : perm.type === 'global';
			} else {
				return true;
			}
		});
	}
});

function togglePermission(perm: string) {
	if (role.value.permissions) {
		if (!role.value.permissions[perm]) {
			delete role.value.permissions[perm];
		}
	}
}
</script>

<style>
.perm:nth-child(odd) {
	background-color: var(--input-bg-color);
}
</style>
