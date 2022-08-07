<template>
    <flex column gap="4">
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
            <flex column class="alt-bg-color p-3">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Level</th>
                            <th>Add Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user of members" :key="user.id">
                            <td><a-user :user="user"/></td>
                            <td>{{memberLevels[user.level]}}</td>
                            <td>{{fullDate(user.created_at)}}</td>
                            <td class="text-center p-1">
                                <flex inline>
                                    <a-button icon="cog" @click.prevent="editMember(user)"/>
                                    <a-button icon="trash" @click.prevent="deleteMember(user)"/>
                                </flex>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </flex>
        </div>
    </flex>

    <a-modal-form v-model="showModal" title="Edit Member" @save="saveMember()">
        <a-user-select v-if="currentMember.created_at == null" v-model="newMemberUser" label="User"/>
        <a-select v-model="currentMember.level" :options="levelOptions" label="Level"/>
    </a-modal-form>

    <a-modal-form v-model="showTransferOwner" :title="$t('transfer_ownership')" :desc="$t('transfer_mod_warning')" @save="transferOwnership()">
        <a-user-select v-model="transferOwner.owner_id" label="User"/>
        <a-select v-model="transferOwner.keep_owner_level" :options="levelOptions" clearable label="Keep as Member of level"/>
    </a-modal-form>
</template>

<script setup lang="ts">
import { Mod, ModMember, TransferRequest, User } from '~~/types/models';
import clone from 'rfdc/default';
import { Ref } from 'vue';
const { init: openModal } = useModal();

const ignoreChanges: () => void = inject('ignoreChanges');
const mod = inject<Mod>('mod');
const canSave = inject<Ref<boolean>>('canSave');
const canSuperUpdate = inject<boolean>('canSuperUpdate');

const levelOptions = [
    {name: 'Maintainer', value: 0},
    {name: 'Collaborator', value: 1},
    {name: 'Viewer', value: 2},
    {name: 'Contributor', value: 3},
];

const showModal = ref(false);
const showTransferOwner = ref(false);
const members = ref<ModMember[]>(clone(mod.members));
const currentMember = ref<ModMember>(clone({ level: 1 }));
const newMemberUser = ref<User>();
const transferOwner = ref({owner_id: null, keep_owner_level: null});

async function deleteMember(member: ModMember) {
    openModal({
        message: 'Are you sure you want to remove member?',
        async onOk() {
            await useDelete(`mods/${mod.id}/members/${member.id}`);
            members.value = members.value.filter(l => l.id !== member.id);
            mod.members = clone(members.value);
        
            if (!canSave.value) {
                ignoreChanges();
            }
        }
    });

}

function newMember() {
    currentMember.value = clone({ level: 1 });
    showModal.value = true;
}

function editMember(member: ModMember) {
    showModal.value = true;
    currentMember.value = member;
}

async function saveMember() {
    let error = null;

    const member = currentMember.value;
    const data = { user_id: member.user_id || newMemberUser.value, level: member.level };

    if (member.created_at) {
        await usePatch(`mods/${mod.id}/members/${member.id}`, data).catch(err => error = err);
    } else {
        const newMember = await usePost<ModMember>(`mods/${mod.id}/members`, data).catch(err => error = err);
        if (error) {
            throw error;
        }
        members.value.push(newMember);
    }

    if (error) {
        throw error;
    }

    for (const m of mod.members) {
        if (m.id === member.id) {
            Object.assign(m, member);
        }
    }

    if (!canSave.value) {
        ignoreChanges();
    }
}

async function transferOwnership() {
    let error = null;
    const request = await usePatch<TransferRequest>(`mods/${mod.id}/owner`, transferOwner.value).catch(err => error = err);
    if (error) {
        throw error;
    } else {
        mod.transfer_request = request;
        if (!canSave.value) {
            ignoreChanges();
        }
    }
}

async function cancelTransferRequest() {
    let error = null;
    await usePatch(`mods/${mod.id}/transfer-request/cancel`).catch(err => error = err);
    if (error) {
        throw error;
    } else {
        mod.transfer_request = null;
        if (!canSave.value) {
            ignoreChanges();
        }
    }
}
</script>

<style scoped>
td {
    vertical-align: middle;
}
</style>