<template>
    <flex v-if="superUpdate" column>
        <a-alert v-if="mod.transfer_request">
            <i18n-t keypath="already_sent_transfer">
                <template #user>
                    <a-user :user="mod.transfer_request.user" avatar-size="xs"/>
                </template>
            </i18n-t>
            <div class="mt-2">
                <a-button @click="cancelTransferRequest">{{$t('cancel')}}</a-button>
            </div>
        </a-alert>
        <div v-else>
            <a-button @click="showTransferOwner = true">{{$t('transfer_ownership')}}</a-button>
        </div>
    </flex>
    <flex column class="overflow-hidden">
        <flex class="items-center mb-2">
            <label>{{$t('members_tab')}}</label>
            <a-button class="ml-auto" @click="newMember()">{{$t('new')}}</a-button>
        </flex>
        <a-table>
            <template #head>
                <th>{{$t('user')}}</th>
                <th>{{$t('member_level')}}</th>
                <th>{{$t('member_accepted')}}</th>
                <th>{{$t('date')}}</th>
                <th class="text-center">{{$t("actions")}}</th>
            </template>
            <template #body>
                <tr v-for="user of members" :key="user.id">
                    <td><a-user :user="user"/></td>
                    <td>{{$t(`member_level_${user.level}`)}}</td>
                    <td>{{user.accepted ? '✔' : '❌'}}</td>
                    <td>{{fullDate(user.created_at)}}</td>
                    <td class="text-center p-1">
                        <flex inline>
                            <a-button icon="mdi:cog" @click.prevent="editMember(user)"/>
                            <a-button icon="mdi:trash" @click.prevent="deleteMember(user)"/>
                        </flex>
                    </td>
                </tr> 
            </template>
        </a-table>
    </flex>


    <a-modal-form v-model="showModal" :title="$t('edit_member')" @submit="saveMember">
        <template v-if="currentMember">
            <a-user-select v-if="currentMember.new" v-model="currentMember.user" value-by="" :label="$t('user')"/>
            <a-select v-model="currentMember.level" :options="levelOptions" :label="$t('member_level')"/>
        </template>
    </a-modal-form>

    <a-modal-form v-model="showTransferOwner" :title="$t('transfer_ownership')" :desc="$t('transfer_mod_warning')" @submit="transferOwnership()">
        <a-user-select v-model="transferOwner.owner_id" :label="$t('user')"/>
        <a-select v-model="transferOwner.keep_owner_level" :options="levelOptions" clearable :label="$t('transfer_keep_as_member')"/>
    </a-modal-form>
</template>

<script setup lang="ts">
import { Mod, ModMember, TransferRequest } from '~~/types/models';
import clone from 'rfdc/default';
import { fullDate } from '~~/utils/helpers';
import { FetchError } from 'ohmyfetch';
import { useI18n } from 'vue-i18n';
const yesNoModal = useYesNoModal();
const showToast = useQuickErrorToast();
const { t } = useI18n();

const ignoreChanges: (() => void)|undefined = inject('ignoreChanges');
const props = defineProps<{
    mod: Mod,
}>();
const superUpdate = inject<boolean>('canSuperUpdate');

const levelOptions = [
    {name: t('member_level_maintainer'), id: 'maintainer'},
    {name: t('member_level_collaborator'), id: 'collaborator'},
    {name: t('member_level_viewer'), id: 'viewer'},
    {name: t('member_level_contributor'), id: 'contributor'},
];

interface EditModMember {
    level: 'collaborator'|'maintainer'|'contributor'|'viewer',
    created_at?: string,
    user?: ModMember,
    new?: boolean,
}

const showModal = ref(false);
const showTransferOwner = ref(false);
const members = ref<ModMember[]>(clone(props.mod.members));
const currentMember = ref<EditModMember>();
const transferOwner = ref({owner_id: null, keep_owner_level: null});

async function deleteMember(member: ModMember) {
    yesNoModal({
        title: t('are_you_sure'),
        desc: t('irreversible_action'),
        async yes() {
            await useDelete(`mods/${props.mod.id}/members/${member.id}`);
            members.value = members.value.filter(l => l.id !== member.id);
            props.mod.members = clone(members.value);

            ignoreChanges?.();
        }
    });
}

function newMember() {
    currentMember.value = { level: 'collaborator', new: true };
    showModal.value = true;
}

function editMember(member: ModMember) {
    showModal.value = true;
    currentMember.value = { level: member.level, user: member };
}

async function saveMember(error: (e) => void) {
    const member = currentMember.value!;
    const data = { user_id: member.user!.id, level: member.level };

    try {
        if (member.new) {
            const newMember = await usePost<ModMember>(`mods/${props.mod.id}/members`, data);
            members.value.push(newMember);
        } else {
            await usePatch(`mods/${props.mod.id}/members/${member.user!.id}`, data);
        }
    
        for (const m of props.mod.members) {
            if (m.id === member.user!.id) {
                Object.assign(m, member);
            }
        }
    
        ignoreChanges?.();
        showModal.value = false;
    } catch(e) {
        error(e);
    }
}

async function transferOwnership() {
    try {
        const request = await usePatch<TransferRequest>(`mods/${props.mod.id}/owner`, transferOwner.value);
        props.mod.transfer_request = request;
        ignoreChanges?.();
    } catch (error) {
        showToast(error as FetchError);
    }
}

async function cancelTransferRequest() {
    try {
        await usePatch(`mods/${props.mod.id}/transfer-request/cancel`);
        props.mod.transfer_request = undefined;
        ignoreChanges?.();
    } catch (error) {
        showToast(error as FetchError);
    }
}
</script>

<style scoped>
td {
    vertical-align: middle;
}
</style>