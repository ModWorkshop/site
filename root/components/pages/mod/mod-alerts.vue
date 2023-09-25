<template>
    <the-tag-notices v-if="mod.tags" :tags="mod.tags"/>
    <flex v-if="hasAlerts" column gap="2">
        <a-alert v-if="mod.suspended" color="danger" :title="$t('suspended')">
            <i18n-t keypath="mod_suspended" tag="span" scope="global">
                <template #reason>
                    <span v-if="mod.last_suspension">
                        {{mod.last_suspension.reason}}
                    </span>
                    <span v-else>{{$t('no_reason')}}</span>
                </template>
                <template #rules>
                    <NuxtLink to="/rules">{{$t('rules').toLowerCase()}}</NuxtLink>
                </template>
                <template #forum>
                    <NuxtLink :to="`/g/${mod.game?.short_name}/forum?category=appeals`">{{$t('forum').toLowerCase()}}</NuxtLink>
                </template>
            </i18n-t>
        </a-alert>
        <a-alert v-if="!mod.has_download" color="warning" :title="$t('downloads_alert')" :desc="$t('downloads_alert_desc')"/>
        <a-alert v-if="memberWaiting" color="warning">
            {{ t('mod_request', [ memberWaitingRole ]) }}
            <flex>
                <a-button @click="acceptMembership(true)">{{ $t('approve') }}</a-button>
                <a-button color="danger" @click="acceptMembership(false)">{{ $t('reject') }}</a-button>
            </flex>
        </a-alert>

        <a-alert v-if="mod.transfer_request && mod.transfer_request.user_id == user?.id" color="warning">
            {{ t('transfer_request') }}
            <flex>
                <a-button @click="acceptTransfer(true)">{{ $t('approve') }}</a-button>
                <a-button color="danger" @click="acceptTransfer(false)">{{ $t('reject') }}</a-button>
            </flex>
        </a-alert>

        <a-alert v-else-if="mod.approved === null" color="info" :title="$t('mod_waiting')">
            <span>
                {{$t('mod_waiting_desc')}}
            </span>
            <mod-approve v-if="canManage" :mod="mod"/>
        </a-alert>
        <a-alert v-else-if="mod.approved === false" color="danger" :title="$t('mod_rejected')" :desc="$t('mod_rejected_desc')"/>
        <a-alert v-else-if="showPublish" color="warning">
            {{$t('publish_mod_desc')}}
            <a-button class="mr-auto" @click="publish"><i-mdi-upload/> {{ $t('publish_mod') }}</a-button>
        </a-alert>
    </flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Mod } from '~~/types/models';
import { remove } from '@antfu/utils';
const props = defineProps<{
    mod: Mod
}>();

const canEdit = computed(() => canEditMod(props.mod));
const { hasPermission, user } = useStore();
const { t } = useI18n();
const canManage = computed(() => hasPermission('manage-mods', props.mod.game));
const showErrorToast = useQuickErrorToast();

const memberWaiting = computed(() =>  user ? props.mod.members.find(member => !member.accepted && member.id === user.id) : null);
const memberWaitingRole = computed(() => {
    const member = memberWaiting.value;
    if (member) {
        return t(`member_level_${member.level}`);
    }
});
const showPublish = computed(() => canEdit.value && !props.mod.published_at && props.mod.visibility == 'public' && props.mod.has_download);

const hasAlerts = computed(() => {
    const mod = props.mod;
    
    if (!mod) {
        return false;
    }

    const transfer = mod.transfer_request && mod.transfer_request.user_id == user?.id;

    return !mod.has_download || !mod.approved || mod.suspended || memberWaiting.value || transfer || showPublish.value;
});

async function acceptMembership(accept: boolean) {
    await patchRequest(`mods/${props.mod.id}/members/accept`, { accept });
    memberWaiting.value!.accepted = accept;
    if (!accept) {
        remove(props.mod.members, memberWaiting.value);
    }
}

async function acceptTransfer(accept: boolean) {
    await patchRequest(`mods/${props.mod.id}/owner/accept`, { accept });
    if (accept) {
        props.mod.user_id = user!.id;
        props.mod.user = user!;
    }
    props.mod.transfer_request = undefined;
}

async function publish() {
    try {
        Object.assign(props.mod, await patchRequest<Mod>(`mods/${props.mod.id}`, { publish: true, ...props.mod }));
    } catch (error) {
        showErrorToast(error);
    }
}
</script>