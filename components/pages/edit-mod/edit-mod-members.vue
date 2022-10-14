<template>
    <flex v-if="canSuperUpdate" column>
        <a-alert v-if="mod.transfer_request">
            You've sent a transfer request to the user: <a-user :user="mod.transfer_request.user" avatar-size="xs"/>
            If you wish to transfer it to a different person, or have changed your mind, cancel the request.
            <div class="mt-2">
                <a-button @click="cancelTransferRequest">Cancel Transfer Request</a-button>
            </div>
        </a-alert>
        <div v-else>
            <a-button @click="showTransferOwner = true">{{$t('transfer_ownership')}}</a-button>
        </div>
    </flex>
    <div>
        <flex class="items-center mb-2">
            <label>Members</label>
            <a-button class="ml-auto" @click="newMember()">New</a-button>
        </flex>
        <a-table>
            <thead>
                <th>User</th>
                <th>Level</th>
                <th>Accepted</th>
                <th>Add Date</th>
                <th class="text-center">Actions</th>
            </thead>
            <tbody>
                <tr v-for="user of members" :key="user.id">
                    <td><a-user :user="user"/></td>
                    <td>{{memberLevels[user.level]}}</td>
                    <td>{{user.accepted ? '✔' : '❌'}}</td>
                    <td>{{fullDate(user.created_at)}}</td>
                    <td class="text-center p-1">
                        <flex inline>
                            <a-button icon="cog" @click.prevent="editMember(user)"/>
                            <a-button icon="trash" @click.prevent="deleteMember(user)"/>
                        </flex>
                    </td>
                </tr> 
            </tbody>
        </a-table>
    </div>

    <a-modal-form v-model="showModal" title="Edit Member" @submit="saveMember">
        {{currentMember.user}}
        <a-user-select v-if="currentMember.created_at == null" v-model="currentMember.user" value-by="" label="User"/>
        <a-select v-model="currentMember.level" :options="levelOptions" label="Level"/>
    </a-modal-form>

    <a-modal-form v-model="showTransferOwner" :title="$t('transfer_ownership')" :desc="$t('transfer_mod_warning')" @submit="transferOwnership()">
        <a-user-select v-model="transferOwner.owner_id" label="User"/>
        <a-select v-model="transferOwner.keep_owner_level" :options="levelOptions" clearable label="Keep as Member of level"/>
    </a-modal-form>
</template>

<script setup lang="ts">
import { Mod, ModMember, TransferRequest } from '~~/types/models';
import clone from 'rfdc/default';
import { fullDate } from '~~/utils/helpers';
import { memberLevels } from '~~/utils/mod-helpers';
const yesNoModal = useYesNoModal();
const { showToast } = useToaster();

const ignoreChanges: () => void = inject('ignoreChanges');
const props = defineProps<{
    mod: Mod,
}>();
const canSuperUpdate = inject<boolean>('canSuperUpdate');

const levelOptions = [
    {name: 'Maintainer', id: 0},
    {name: 'Collaborator', id: 1},
    {name: 'Viewer', id: 2},
    {name: 'Contributor', id: 3},
];

interface EditModMember {
    level: number;
    created_at?: string,
    user?: ModMember;
}

const showModal = ref(false);
const showTransferOwner = ref(false);
const members = ref<ModMember[]>(clone(props.mod.members));
const currentMember = ref<EditModMember>();
const transferOwner = ref({owner_id: null, keep_owner_level: null});

async function deleteMember(member: ModMember) {
    yesNoModal({
        desc: 'Are you sure you want to remove member?',
        async yes() {
            await useDelete(`mods/${props.mod.id}/members/${member.id}`);
            members.value = members.value.filter(l => l.id !== member.id);
            props.mod.members = clone(members.value);

            ignoreChanges();
        }
    });
}

function newMember() {
    currentMember.value = { level: 1 };
    showModal.value = true;
}

function editMember(member: ModMember) {
    showModal.value = true;
    currentMember.value = { level: member.level, user: member };
}

async function saveMember(error: (e) => void) {
    const member = currentMember.value;
    const data = { user_id: member.user.id, level: member.level };

    try {
        if (member.created_at) {
            await usePatch(`mods/${props.mod.id}/members/${member.user.id}`, data);
        } else {
            const newMember = await usePost<ModMember>(`mods/${props.mod.id}/members`, data);
            members.value.push(newMember);
        }
    
        for (const m of props.mod.members) {
            if (m.id === member.user.id) {
                Object.assign(m, member);
            }
        }
    
        ignoreChanges();
        showModal.value = false;
    } catch(e) {
        error(e);
    }
}

async function transferOwnership() {
    try {
        const request = await usePatch<TransferRequest>(`mods/${props.mod.id}/owner`, transferOwner.value);
            props.mod.transfer_request = request;
        ignoreChanges();
    } catch (error) {
        showToast({ desc: error.message, color: 'danger' });
    }
}

async function cancelTransferRequest() {
    try {
        await usePatch(`mods/${props.mod.id}/transfer-request/cancel`);
        props.mod.transfer_request = null;
        ignoreChanges();
    } catch (error) {
        showToast({ desc: error.message, color: 'danger' });
    }
}
</script>

<style scoped>
td {
    vertical-align: middle;
}
</style>