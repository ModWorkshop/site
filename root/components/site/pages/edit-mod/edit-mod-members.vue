<template>
    <m-flex v-if="superUpdate" column>
        <m-alert v-if="mod.transfer_request">
            <i18n-t keypath="already_sent_transfer" scope="global">
                <template #user>
                    <a-user :user="mod.transfer_request.user" avatar-size="xs"/>
                </template>
            </i18n-t>
            <div class="mt-2">
                <m-button @click="cancelTransferRequest">{{$t('cancel')}}</m-button>
            </div>
        </m-alert>
        <div v-else>
            <m-button @click="showTransferOwner = true">{{$t('transfer_ownership')}}</m-button>
        </div>
    </m-flex>
    <m-flex column class="overflow-hidden">
        <m-flex class="items-center">
            <label>{{$t('members_tab')}}</label>
            <m-button v-if="levelOptions" class="ml-auto" @click="newMember()">{{$t('new')}}</m-button>
        </m-flex>
        <m-table alt-background>
            <template #head>
                <th>{{$t('user')}}</th>
                <th>{{$t('member_level')}}</th>
                <th>{{$t('member_accepted')}}</th>
                <th>{{$t('date')}}</th>
                <th class="text-center">{{$t("actions")}}</th>
            </template>
            <template #body>
                <template v-if="members.length">
                    <tr v-for="m of members" :key="m.id">
                        <td><a-user :user="m"/></td>
                        <td>{{$t(`member_level_${m.level}`)}}</td>
                        <td>{{m.accepted ? '✔' : '❌'}}</td>
                        <td>{{mod.id ? fullDate(m.created_at) : $t('waiting_for_mod')}}</td>
                        <td class="text-center p-1">
                            <m-flex inline>
                                <m-button :disabled="!canEditMember(m)" @click.prevent="editMember(m)"><i-mdi-cog/></m-button>
                                <m-button :disabled="!canEditMember(m)" @click.prevent="deleteMember(m)"><i-mdi-delete/></m-button>
                            </m-flex>
                        </td>
                    </tr>
                </template>
                <tr v-else>
                    <td colspan="5" class="text-center">
                        {{$t('nothing_found')}}
                    </td>
                </tr>
            </template>
        </m-table>
    </m-flex>


    <m-form-modal v-model="showModal" :title="$t('edit_member')" @submit="saveMember">
        <user-select v-if="addingNew" v-model="selectedUser" required value-by="" :label="$t('user')"/>
        <m-select v-model="selectedLevel" :options="levelOptions" :label="$t('member_level')"/>
    </m-form-modal>

    <m-form-modal v-model="showTransferOwner" :title="$t('transfer_ownership')" :desc="$t('transfer_mod_warning')" @submit="transferOwnership()">
        <user-select v-model="transferOwner.owner_id" :label="$t('user')"/>
        <m-select v-model="transferOwner.keep_owner_level" :options="levelOptions" clearable :label="$t('transfer_keep_as_member')"/>
    </m-form-modal>
</template>

<script setup lang="ts">
import type { Mod, ModMember, TransferRequest, User } from '~~/types/models';
import clone from 'rfdc/default';
import { fullDate } from '~~/utils/helpers';
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store/index';
const yesNoModal = useYesNoModal();
const showToast = useQuickErrorToast();
const { t } = useI18n();
const { user } = useStore();

const mod = defineModel<Mod>({ required: true });

const superUpdate = inject<boolean>('canSuperUpdate');

const MOD_MEMBER_RULES_OVER = {
    owner: ['maintainer', 'collaborator', 'contributor', 'viewer'],
    maintainer: ['collaborator', 'contributor', 'viewer'],
    collaborator: ['contributor', 'viewer'],
};

const memberPerms = computed(() => {
    if (!superUpdate && !member.value?.level) {
        return null;
    }

    return MOD_MEMBER_RULES_OVER[superUpdate ? 'owner' : member.value!.level];
});

const levelOptions = computed(() => {
    if (!superUpdate && (!member.value || (member.value.level !== 'maintainer' && member.value.level !== 'collaborator'))) {
        return undefined;
    }

    const levels = [
        {name: t('member_level_viewer'), id: 'viewer'},
        {name: t('member_level_contributor'), id: 'contributor'},
    ];

    if (superUpdate) {
        levels.push({name: t('member_level_maintainer'), id: 'maintainer'});
    }

    if (superUpdate || member.value?.level == 'maintainer') {
        levels.push({name: t('member_level_collaborator'), id: 'collaborator'});
    }

    return levels;
});

const showModal = ref(false);
const addingNew = ref(false);
const showTransferOwner = ref(false);
const members = ref<ModMember[]>(clone(mod.value.members));
const transferOwner = ref({owner_id: null, keep_owner_level: null});
const member = computed(() => user ? mod.value.members.find(member => member.id == user.id) : null);

const selectedUser = ref<User>();
const selectedLevel = ref<'collaborator'|'maintainer'|'contributor'|'viewer'>('collaborator');

watch(() => mod.value.id, async () => {
    if (mod.value.id) {
        for (const member of members.value) {
            const newMember = await postRequest<ModMember>(`mods/${mod.value.id}/members`, { user_id: member.id, level: member.level });
            Object.assign(member, newMember);
        }
    }
});

async function deleteMember(member: ModMember) {
    if (mod.value.id) {
        yesNoModal({
            title: t('are_you_sure'),
            desc: t('irreversible_action'),
            async yes() {
                await deleteRequest(`mods/${mod.value.id}/members/${member.id}`);
                members.value = members.value.filter(l => l!.id !== member.id);
                mod.value.members = clone(members.value);
            }
        });
    } else {
        members.value = members.value.filter(l => l!.id !== member.id);
        mod.value.members = clone(members.value);
    }
}

function newMember() {
    addingNew.value = true;
    showModal.value = true;

    selectedUser.value = undefined;
    selectedLevel.value = 'collaborator';
}

function editMember(member: ModMember) {
    showModal.value = true;
    addingNew.value = false;

    selectedUser.value = member;
}

async function saveMember(error: (e) => void) {
    const user = selectedUser.value!;
    const level = selectedLevel.value;

    try {
        if (addingNew.value) {
            if (mod.value.id) {
                const newMember = await postRequest<ModMember>(`mods/${mod.value.id}/members`, { user_id: user.id, level });
                members.value.push(newMember);
            } else {
                members.value.push({ level, accepted: false, ...user});
            }
        } else if (mod.value.id) {
            await patchRequest(`mods/${mod.value.id}/members/${user.id}`, { level });
        }
    
        for (const m of members.value) {
            if (m.id === user.id) {
                m.level = level;
            }
        }
    
        showModal.value = false;
    } catch(e) {
        error(e);
    }
}

async function transferOwnership() {
    try {
        const request = await patchRequest<TransferRequest>(`mods/${mod.value.id}/owner`, transferOwner.value);
        mod.value.transfer_request = request;
        showTransferOwner.value = false;
    } catch (error) {
        showToast(error);
    }
}

async function cancelTransferRequest() {
    try {
        await patchRequest(`mods/${mod.value.id}/owner/cancel`);
        mod.value.transfer_request = undefined;
    } catch (error) {
        showToast(error);
    }
}

function canEditMember(member: ModMember) {
    return (user && member.id == user.id) || (memberPerms.value ? memberPerms.value.includes(member.level) : false);
}
</script>

<style scoped>
td {
    vertical-align: middle;
}
</style>