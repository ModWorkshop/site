<template>
    <flex column gap="4">
        <div>
            <flex class="items-center">
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
                        <tr v-for="user of mod.members" :key="user.id">
                            <td><a-user :user="user"/></td>
                            <td>{{levels[user.level]}}</td>
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

    <client-only>
        <va-modal v-model="showModal" size="large" background-color="#2b3036" no-outside-dismiss>
            <template #content="{ ok }">
                <flex v-if="currentMember" column gap="2">
                    <h2>Edit Member</h2>
                    <a-user-select v-if="currentMember.created_at == null" v-model="newMemberUser" label="User"/>
                    <a-select v-model="currentMember.level" :options="levelOptions" label="Level"/>
                    <a-error-alert :error="error"/>
                    <flex>
                        <a-button @click="saveMember(currentMember, ok)">Save</a-button>
                        <a-button color="danger" @click="ok">Cancel</a-button>
                    </flex>
                </flex>
            </template>
        </va-modal>
    </client-only>
</template>

<script setup lang="ts">
import { Mod, ModMember, User } from '~~/types/models';

const props = defineProps<{
    mod: Mod,
    canSave: boolean
}>();

const ignoreChanges: () => void = inject('ignoreChanges');

const levels = [
    'Maintainer',
    'Collaborator',
    'Viewer',
    'Contributor',
];

const levelOptions = [
    {name: 'Maintainer', value: 0},
    {name: 'Collaborator', value: 1},
    {name: 'Viewer', value: 2},
    {name: 'Contributor', value: 3},
];


const showModal = ref(false);
const error = ref();
const currentMember = ref<ModMember>({ level: 1 });
const newMemberUser = ref<User>();

async function deleteMember(member: ModMember) {
    await useDelete(`mods/${props.mod.id}/members/${member.id}`);
    props.mod.links = props.mod.links.filter(l => l.id !== member.id);

    if (!props.canSave) {
        ignoreChanges();
    }
}

function newMember() {
    currentMember.value = { level: 1 };
    showModal.value = true;
}

function editMember(member: ModMember) {
    showModal.value = true;
    currentMember.value = member;
}

async function saveMember(member: ModMember, ok: () => void) {
    error.value = null;

    const data = { user_id: member.user_id || newMemberUser.value, level: member.level };

    if (member.created_at) {
        await usePatch(`mods/${props.mod.id}/members/${member.id}`, data).catch(err => error.value = err);
    } else {
        await usePost(`mods/${props.mod.id}/members`, data).catch(err => error.value = err);
    }
    
    if(error.value) {
        return;
    }

    for (const m of props.mod.members) {
        if (m.id === member.id) {
            Object.assign(m, member);
        }
    }

    if (!props.canSave) {
        ignoreChanges();
    }

    ok();
}
</script>

<style scoped>
td {
    vertical-align: middle;
}
</style>