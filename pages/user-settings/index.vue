<template>
    <flex gap="2" column>
        <a-alert v-if="!user.signable" color="warning" :title="$t('sso_only_warning')" :desc="$t('sso_only_warning_desc')"/>
        <a-input v-model="user.name" :label="$t('display_name')"/>
        <a-input v-model="user.unique_name" :label="$t('unique_name')" :desc="$t('unique_name_desc')"/>
        <a-input v-if="user.email || isMe" v-model="user.email" :label="$t('email')" :disabled="!isMe"/>
        <a-input :label="$t('roles')">
            <flex gap="3" class="p-4 input-bg" column>
                <a-tag-selector
                    v-model="roleIds"
                    multiple
                    :label="$t('role')"
                    :options="roles?.data"
                    :disabled="user.id !== me!.id && !hasPermission('manage-roles')"
                    :enabled-by="role => role.assignable"
                    :color-by="item => item.color"
                    @update:model-value="prepareSaveRoles"
                />
                <a-select v-model="selectedGame" :label="$t('games')" url="games" clearable/>
                <a-tag-selector 
                    v-if="gameUserData"
                    v-model="gameUserData.role_ids"
                    multiple
                    :disabled="user.id !== me!.id && !hasPermission('manage-roles', selectedGame)"
                    :options="gameRoles?.data" 
                    :enabled-by="role => role.assignable"
                    :color-by="item => item.color"
                    @update:model-value="prepareSaveGameRoles"
                />
            </flex>
        </a-input>

        <template v-if="isMe">
            <h3>{{$t('change_password')}}</h3>
            <flex>
                <a-input 
                    v-if="user.signable"
                    v-model="user.current_password"
                    autocomplete="off"
                    :label="$t('current_password')"
                    type="password"
                    minlength="12"
                    maxlength="128"
                />
                <a-input 
                    v-model="user.password"
                    :validity="passValidity"
                    autocomplete="off"
                    :label="$t('new_password')"
                    type="password"
                    minlength="12"
                    maxlength="128"
                />
                <a-input
                    v-model="user.confirm_password"
                    :validity="confirmPassValidity"
                    :label="$t('confirm_password')"
                    type="password"
                    minlength="12"
                    maxlength="128"
                />
            </flex>
            <small>{{$t('password_guide')}}</small>
            <a-link-button disabled @click="reset">{{$t('forgot_password_button')}}</a-link-button>
        </template>
    
        <a-alert color="info" :title="$t('request_my_data')">
            {{$t('request_my_data_desc')}}
            <a ref="downloadDataButton" download :href="`${getApiURL(config)}/user-data`"/>
            <div>
                <a-button @click="downloadData">{{$t('download')}}</a-button>
            </div>
        </a-alert>

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
import { passwordValidity } from '~~/utils/helpers';

const props = defineProps<{
    user: UserForm
}>();

const { public: config } = useRuntimeConfig();

const { t } = useI18n();
const { user: me, hasPermission } = useStore();

const store = useStore();

const { showToast } = useToaster();

const selectedGame = useRouteQuery('game', undefined, 'number');

const roleIds = ref(clone(props.user.role_ids));
const downloadDataButton = ref<HTMLAnchorElement>();

const { data: roles } = await useFetchMany<Role>('roles', { params: { only_assignable: 1 } });

const { start: prepareSaveGameRoles } = useTimeoutFn(saveGameRoles, 500, { immediate: false });
const { start: prepareSaveRoles } = useTimeoutFn(saveRoles, 500, { immediate: false });

const { data: gameRoles, refresh } = await useFetchMany<Role>(() => `games/${selectedGame.value}/roles`, { immediate: !!selectedGame.value });
const { data: gameUserData, refresh: loadGameData } = await useFetchData<GameUserData>(() => `games/${selectedGame.value}/users/${props.user.id}`, { 
    immediate: !!selectedGame.value
});

function downloadData() {
    if (downloadDataButton.value) {
        downloadDataButton.value.click();
    }
}

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
    await usePatch(`games/${selectedGame.value}/users/${props.user.id}/roles`, { role_ids: gameUserData.value?.role_ids });
}

async function saveRoles() {
    await usePatch(`users/${props.user.id}/roles`, { role_ids: roleIds.value });
}

const yesNoModal = useYesNoModal();
const isMe = inject<Ref<boolean>>('isMe');

async function doDelete() {
    yesNoModal({
        title: t('are_you_sure'),
        desc: t('irreversible_action'),
        async yes() {
            await useDelete(`users/${props.user.id}`);
            await store.logout();
        }
    });
}

async function reset() {
    if (me) {
        await usePost('forgot-password', {
            email: me.email
        });

        showToast({
            color: 'success',
            desc: t('password_reset_sent_unknown')
        });
    }
}
</script>