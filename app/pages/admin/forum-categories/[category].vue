<template>
    <simple-resource-form v-model="category" url="forum-categories" :game="game" :redirect-to="redirectUrl">
        <m-input v-model="category.name" required :label="$t('name')"/>
        <m-input v-model="category.emoji" max="1" :label="$t('emoji')"/>
        <m-input v-model="category.display_order" :label="$t('order')" type="number"/>

        <md-editor v-model="category.desc" :label="$t('description')"/>
        <m-flex column>
            <label>{{$t('role_policies')}}</label>
            <m-flex>
                <m-select v-model="addRole" :options="roles?.data"/>
                <m-button :disabled="!addRole" @click="addRolePolicy">{{$t('add')}}</m-button>
            </m-flex>
            <m-table alt-background>
                <template #head>
                    <th>{{$t('role')}}</th>
                    <th>{{$t('forum_category_can_view')}}</th>
                    <th>{{$t('forum_category_can_post')}}</th>
                    <th>{{$t('actions')}}</th>
                </template>
                <template #body>
                    <tr v-for="addedRole in addedRoles" :key="addedRole.id">
                        <td>{{addedRole.role.name}}</td>
                        <td><m-input v-model="addedRole.policy.can_view" type="checkbox"/></td>
                        <td><m-input v-model="addedRole.policy.can_post" type="checkbox"/></td>
                        <td><m-button color="danger" @click="removeRolePolicy(addedRole.id)"><i-mdi-delete/> {{$t('delete')}}</m-button></td>
                    </tr>
                </template>
            </m-table>
        </m-flex>
        <m-flex v-if="game" column class="p-1">
            <label>{{$t('game_role_policies')}}</label>
            <m-flex>
                <m-select v-model="addGameRole" :options="validGameRoles"/>
                <m-button :disabled="!addGameRole" @click="addGameRolePolicy">{{$t('add')}}</m-button>
            </m-flex>
            <m-table alt-background>
                <template #head>
                    <th>{{$t("role")}}</th>
                    <th>{{$t('forum_category_can_view')}}</th>
                    <th>{{$t('forum_category_can_post')}}</th>
                    <th>{{$t('actions')}}</th>
                </template>
                <template #body>
                    <tr v-for="addedRole in addedGameRoles" :key="addedRole.id">
                        <td>{{addedRole.role.name}}</td>
                        <td><m-input v-model="addedRole.policy.can_view" type="checkbox"/></td>
                        <td><m-input v-model="addedRole.policy.can_post" type="checkbox"/></td>
                        <td><m-button color="danger" @click="removeGameRolePolicy(addedRole.id)"><i-mdi-delete/> {{$t('delete')}}</m-button></td>
                    </tr>
                </template>
            </m-table>
        </m-flex>
        <m-input v-model="category.is_private" type="checkbox" :label="$t('private_category')"/>
        <m-input v-model="category.banned_can_post" type="checkbox" :label="$t('banned_can_post')"/>
        <m-input v-model="category.private_threads" type="checkbox" :label="$t('private_threads')"/>
        <m-input v-model="category.can_close_threads" type="checkbox" :label="$t('can_close_threads')"/>
        <m-input v-model="category.hidden" type="checkbox" :label="$t('hidden')"/>
        <m-input v-model="category.grid_mode" type="checkbox" :label="$t('grid_mode')"/>
    </simple-resource-form>
</template>

<script setup lang="ts">
import type { ForumCategory, Game, Role, RolePolicy } from "~/types/models";

const props = defineProps<{
    game?: Game
}>();

useNeedsPermission('manage-forum-categories', props.game);

const forumId = computed(() => props.game?.forum_id ?? 1);
const addRole = ref<number>();
const addGameRole = ref<number>();
const redirectUrl = computed(() => getAdminUrl('forum-categories', props.game));

const { data: roles } = await useFetchMany<Role>('roles', { 
    params: {
        filter: { is_vanity: false }
    }
});

const { data: category } = await useEditResource<ForumCategory>('category', 'forum-categories', {
    id: 0,
    name: '',
    forum_id: forumId.value,
    desc: "",
    emoji: '',
    created_at: "",
    updated_at: "",
    is_private: false,
    hidden: false,
    display_order: 0,
    banned_can_post: false,
    private_threads: false,
    role_policies: {},
    game_role_policies: {},
});

const addedRoles = computed(() => {
    const arr: { id: number, role: Role, policy: RolePolicy }[] = [];

    if (category.value.role_policies) {
        for (const [id, policy] of Object.entries(category.value.role_policies)) {
            const role = roles.value?.data.find(r => r.id === parseInt(id));
            if (role) {
                arr.push({ id: parseInt(id), role, policy });
            }
        }
    }

    return arr;
});

const addedGameRoles = computed(() => {
    const arr: { id: number, role: Role, policy: RolePolicy }[] = [];

    if (category.value.game_role_policies) {
        for (const [id, policy] of Object.entries(category.value.game_role_policies)) {
            const role = props.game!.roles?.find(r => r.id === parseInt(id));
            if (role) {
                arr.push({ id: parseInt(id), role, policy });
            }
        }
    }

    return arr;
});

const validGameRoles = computed(() => props.game!.roles?.filter(role => !role.is_vanity));

function addRolePolicy() {
    if (category.value.role_policies) {
        category.value.role_policies[addRole.value!] = { can_view: false, can_post: false };
    }
}

function removeRolePolicy(roleId: number) {
    if (category.value.role_policies) {
        delete category.value.role_policies[roleId];
    }
}

function addGameRolePolicy() {
    if (category.value.game_role_policies) {
        category.value.game_role_policies[addGameRole.value!] = { can_view: false, can_post: false };
    }
}

function removeGameRolePolicy(roleId: number) {
    if (category.value.game_role_policies) {
        delete category.value.game_role_policies[roleId];
    }
}
</script>