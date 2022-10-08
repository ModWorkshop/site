<template>
    <flex gap="2" column>
        <a-alert v-if="!user.signable" color="warning" :title="$t('sso_only_warning')" :desc="$t('sso_only_warning_desc')"/>
        <a-input v-model="user.name" label="Username"/>
        <a-input v-model="user.unique_name" label="Unique Name" desc="A unique name for your profile and to allow people to mention you."/>
        <a-input v-if="user.email || isMe" v-model="user.email" label="Email" :disabled="!isMe"/>
        <a-input :label="$t('roles')">
            <flex gap="3" class="p-4 input-bg" column>
                <a-tag-selector
                    v-model="roleIds"
                    multiple
                    :label="$t('global')"
                    :options="roles?.data"
                    :disabled="user.id !== me.id && !hasPermission('manage-roles')"
                    :enabled-by="role => role.assignable"
                    :color-by="item => item.color"
                    @update:model-value="prepareSaveRoles"
                />
                <a-select v-model="selectedGame" :label="$t('games')" url="games" clearable/>
                <a-tag-selector 
                    v-if="selectedGame"
                    v-model="gameUserData.role_ids"
                    multiple
                    :disabled="user.id !== me.id && !hasPermission('manage-roles', selectedGame)"
                    :options="gameRoles?.data" 
                    :enabled-by="role => role.assignable"
                    :color-by="item => item.color"
                    @update:model-value="prepareSaveGameRoles"
                />
            </flex>
        </a-input>

        <template v-if="isMe">
            <h3>Change Password</h3>
            <flex>
                <a-input v-model="user.current_password" :validity="passValidity" label="Current Password" type="password" minlength="12" maxlength="128"/>
                <a-input v-model="user.password" :validity="passValidity" label="New Password" type="password" minlength="12" maxlength="128"/>
                <a-input v-model="user.confirm_password" :validity="confirmPassValidity" label="Confirm Password" type="password" minlength="12" maxlength="128"/>
            </flex>
            <small>{{$t('password_guide')}}</small>
        </template>
    
        <a-alert class="w-full" color="danger" :title="$t('danger_zone')">
            <div>
                <a-button color="danger" @click="doDelete">{{$t('delete')}}</a-button>
            </div>
        </a-alert>
    </flex>
</template>

<script setup lang="ts">
import { Ref } from 'vue';
import { useStore } from '~~/store';
import { GameUserData, Role, UserForm } from '~~/types/models';
import clone from 'rfdc/default';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
    user: UserForm
}>();

const { t } = useI18n();
const { user: me, hasPermission } = useStore();
const selectedGame = useRouteQuery('game', undefined, 'number');

const roleIds = ref(clone(props.user.role_ids));

const { data: roles } = await useFetchMany<Role>('roles', { params: { only_assignable: 1 }, initialCache: true });

const { start: prepareSaveGameRoles } = useTimeoutFn(saveGameRoles, 500, { immediate: false });
const { start: prepareSaveRoles } = useTimeoutFn(saveRoles, 500, { immediate: false });

const { data: gameRoles, refresh } = await useFetchMany<Role>(() => `games/${selectedGame.value}/roles`, { immediate: !!selectedGame.value });
const { data: gameUserData, refresh: loadGameData } = await useFetchData<GameUserData>(() => `games/${selectedGame.value}/users/${props.user.id}`, { 
    immediate: !!selectedGame.value
});

const passValidity = computed(() => {
    const validity = passwordValidity(props.user.password);
    if (validity) {
        return t(validity);
    }
});

const confirmPassValidity = computed(() => {
    if (props.user.confirm_password && props.user.password !== props.user.confirm_password) {
        return t('password_error_match');
    }
});

watch(selectedGame, () => {
    if (selectedGame.value) {
        refresh();
        loadGameData();
    }
});

async function saveGameRoles() {
    await usePatch(`games/${selectedGame.value}/users/${props.user.id}/roles`, { role_ids: gameUserData.value.role_ids });
}

async function saveRoles() {
    await usePatch(`users/${props.user.id}/roles`, { role_ids: roleIds.value });
}

const store = useStore();
const yesNoModal = useYesNoModal();
const isMe = inject<Ref<boolean>>('isMe');

async function doDelete() {
    yesNoModal({
        title: 'Are you sure?',
        desc: 'This action is irreversible!',
        async yes() {
            await useDelete(`users/${props.user.id}`);
            await store.logout();
        }
    });
}
</script>